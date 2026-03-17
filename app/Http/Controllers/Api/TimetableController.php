<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Academic\Timetable;
use App\Models\Academic\Division;
use App\Models\Academic\Subject;
use App\Models\Academic\TimeSlot;
use App\Models\Academic\AcademicYear;
use App\Models\User;
use App\Models\Holiday;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Timetable API Controller
 * 
 * Handles all timetable CRUD operations via REST API
 */
class TimetableController extends Controller
{
    protected HolidayService $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
        
        // Apply auth middleware
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * GET /api/timetables
     * Display timetable for a division with optional date filter
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'division_id' => 'required|exists:divisions,id',
                'date' => 'nullable|date',
                'week_start_date' => 'nullable|date',
            ]);

            $divisionId = $request->division_id;
            $date = $request->date ? Carbon::parse($request->date) : null;
            $academicYearId = $request->academic_year_id ?? AcademicYear::getCurrentAcademicYearId();

            // Check if date is a holiday
            if ($date) {
                $holidayCheck = $this->holidayService->checkTimetableAvailability($date, $academicYearId);
                
                if ($holidayCheck['status'] === 'holiday') {
                    return response()->json([
                        'success' => true,
                        'is_holiday' => true,
                        'message' => $holidayCheck['message'],
                        'holiday_title' => $holidayCheck['holiday_title'],
                        'date' => $date->format('Y-m-d'),
                        'periods' => [],
                    ]);
                }
            }

            // Build query
            $query = Timetable::with(['division', 'subject', 'teacher', 'room', 'academicYear'])
                ->byDivision($divisionId)
                ->byAcademicYear($academicYearId)
                ->byStatus('active')
                ->notBreakTime();

            // Filter by date if provided
            if ($date) {
                $dayOfWeek = strtolower($date->format('l'));
                $query->where(function ($q) use ($date, $dayOfWeek) {
                    $q->whereDate('date', $date)
                      ->orWhere('day_of_week', $dayOfWeek);
                });
            }

            $timetables = $query->ordered()->get();

            // Format response
            $formattedTimetables = $timetables->map(function ($timetable) {
                return [
                    'id' => $timetable->id,
                    'division_id' => $timetable->division_id,
                    'division_name' => $timetable->division->division_name ?? 'N/A',
                    'subject_id' => $timetable->subject_id,
                    'subject_name' => $timetable->subject->name ?? 'N/A',
                    'subject_code' => $timetable->subject->code ?? 'N/A',
                    'teacher_id' => $timetable->teacher_id,
                    'teacher_name' => $timetable->teacher->name ?? 'No Teacher',
                    'room_id' => $timetable->room_id,
                    'room_number' => $timetable->room->room_number ?? $timetable->room_number ?? 'TBA',
                    'day_of_week' => $timetable->day_of_week,
                    'day_name' => $timetable->day_name,
                    'day_color' => $timetable->day_color,
                    'date' => $timetable->date?->format('Y-m-d'),
                    'start_time' => $timetable->start_time?->format('H:i'),
                    'end_time' => $timetable->end_time?->format('H:i'),
                    'formatted_time' => $timetable->formatted_time_range,
                    'period_name' => $timetable->period_name,
                    'status' => $timetable->status,
                    'notes' => $timetable->notes,
                    'is_specific_date' => $timetable->date !== null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedTimetables,
                'date' => $date?->format('Y-m-d'),
                'division_id' => $divisionId,
                'total_periods' => $formattedTimetables->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Timetable index error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load timetable',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/timetables
     * Store a new timetable entry
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validated = $request->validate([
                'division_id' => 'required|exists:divisions,id',
                'subject_id' => 'required|exists:subjects,id',
                'teacher_id' => 'required|exists:users,id',
                'room_id' => 'nullable|exists:rooms,id',
                'day_of_week' => 'required_without:date|string',
                'date' => 'nullable|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'period_name' => 'nullable|string|max:50',
                'room_number' => 'nullable|string|max:50',
                'academic_year_id' => 'required|exists:academic_years,id',
                'status' => 'nullable|in:active,cancelled,completed',
                'notes' => 'nullable|string',
            ]);

            // Check if date is a holiday
            if (isset($validated['date'])) {
                $holidayCheck = $this->holidayService->validateAttendanceDate(
                    $validated['date'],
                    $validated['academic_year_id']
                );

                if ($holidayCheck['is_holiday']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This date is marked as Holiday. Attendance and Timetable cannot be added.',
                        'holiday_title' => $holidayCheck['holiday_title'],
                        'holiday_type' => $holidayCheck['holiday_type'],
                    ], 422);
                }
            }

            // Check for conflicts
            $conflicts = $this->checkConflicts(
                $validated['division_id'],
                $validated['teacher_id'],
                $validated['room_id'] ?? null,
                $validated['date'] ?? null,
                $validated['day_of_week'],
                $validated['start_time'],
                $validated['end_time']
            );

            if ($conflicts['has_conflicts']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule conflict detected',
                    'conflicts' => $conflicts['details'],
                ], 422);
            }

            // Create timetable
            DB::beginTransaction();

            $timetable = Timetable::create([
                'division_id' => $validated['division_id'],
                'subject_id' => $validated['subject_id'],
                'teacher_id' => $validated['teacher_id'],
                'room_id' => $validated['room_id'] ?? null,
                'day_of_week' => strtolower($validated['date'] 
                    ? Carbon::parse($validated['date'])->format('l') 
                    : $validated['day_of_week']),
                'date' => $validated['date'] ? Carbon::parse($validated['date'])->format('Y-m-d') : null,
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'period_name' => $validated['period_name'] ?? null,
                'room_number' => $validated['room_number'] ?? null,
                'academic_year_id' => $validated['academic_year_id'],
                'status' => $validated['status'] ?? 'active',
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();

            $timetable->load(['division', 'subject', 'teacher', 'room']);

            return response()->json([
                'success' => true,
                'message' => 'Timetable entry created successfully',
                'data' => [
                    'id' => $timetable->id,
                    'division_name' => $timetable->division->division_name,
                    'subject_name' => $timetable->subject->name,
                    'teacher_name' => $timetable->teacher->name ?? 'N/A',
                    'day_name' => $timetable->day_name,
                    'date' => $timetable->date?->format('Y-m-d'),
                    'time' => $timetable->formatted_time_range,
                ],
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Timetable store error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create timetable entry',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/timetables/{id}
     * Display a specific timetable entry
     */
    public function show(Timetable $timetable): JsonResponse
    {
        try {
            $timetable->load(['division', 'subject', 'teacher', 'room', 'academicYear']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $timetable->id,
                    'division_id' => $timetable->division_id,
                    'division_name' => $timetable->division->division_name ?? 'N/A',
                    'subject_id' => $timetable->subject_id,
                    'subject_name' => $timetable->subject->name ?? 'N/A',
                    'subject_code' => $timetable->subject->code ?? 'N/A',
                    'teacher_id' => $timetable->teacher_id,
                    'teacher_name' => $timetable->teacher->name ?? 'No Teacher',
                    'room_id' => $timetable->room_id,
                    'room_number' => $timetable->room->room_number ?? $timetable->room_number ?? 'TBA',
                    'day_of_week' => $timetable->day_of_week,
                    'day_name' => $timetable->day_name,
                    'day_color' => $timetable->day_color,
                    'date' => $timetable->date?->format('Y-m-d'),
                    'start_time' => $timetable->start_time?->format('H:i'),
                    'end_time' => $timetable->end_time?->format('H:i'),
                    'formatted_time' => $timetable->formatted_time_range,
                    'period_name' => $timetable->period_name,
                    'room_number' => $timetable->room_number,
                    'academic_year_id' => $timetable->academic_year_id,
                    'status' => $timetable->status,
                    'notes' => $timetable->notes,
                    'created_at' => $timetable->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $timetable->updated_at?->format('Y-m-d H:i:s'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Timetable show error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load timetable entry',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT /api/timetables/{id}
     * Update a specific timetable entry
     */
    public function update(Request $request, Timetable $timetable): JsonResponse
    {
        try {
            // Validate request
            $validated = $request->validate([
                'division_id' => 'sometimes|required|exists:divisions,id',
                'subject_id' => 'sometimes|required|exists:subjects,id',
                'teacher_id' => 'sometimes|required|exists:users,id',
                'room_id' => 'nullable|exists:rooms,id',
                'day_of_week' => 'required_without:date|string',
                'date' => 'nullable|date',
                'start_time' => 'sometimes|required|date_format:H:i',
                'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
                'period_name' => 'nullable|string|max:50',
                'room_number' => 'nullable|string|max:50',
                'academic_year_id' => 'sometimes|required|exists:academic_years,id',
                'status' => 'nullable|in:active,cancelled,completed',
                'notes' => 'nullable|string',
            ]);

            // Check if date is a holiday
            if (isset($validated['date'])) {
                $holidayCheck = $this->holidayService->validateAttendanceDate(
                    $validated['date'],
                    $validated['academic_year_id'] ?? $timetable->academic_year_id
                );

                if ($holidayCheck['is_holiday']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot update timetable on holiday',
                        'holiday_title' => $holidayCheck['holiday_title'],
                    ], 422);
                }
            }

            // Prepare update data
            $updateData = [];
            
            if (isset($validated['division_id'])) $updateData['division_id'] = $validated['division_id'];
            if (isset($validated['subject_id'])) $updateData['subject_id'] = $validated['subject_id'];
            if (isset($validated['teacher_id'])) $updateData['teacher_id'] = $validated['teacher_id'];
            if (isset($validated['room_id'])) $updateData['room_id'] = $validated['room_id'];
            if (isset($validated['start_time'])) $updateData['start_time'] = $validated['start_time'];
            if (isset($validated['end_time'])) $updateData['end_time'] = $validated['end_time'];
            if (isset($validated['period_name'])) $updateData['period_name'] = $validated['period_name'];
            if (isset($validated['room_number'])) $updateData['room_number'] = $validated['room_number'];
            if (isset($validated['academic_year_id'])) $updateData['academic_year_id'] = $validated['academic_year_id'];
            if (isset($validated['status'])) $updateData['status'] = $validated['status'];
            if (isset($validated['notes'])) $updateData['notes'] = $validated['notes'];
            
            // Handle date and day_of_week
            if (isset($validated['date'])) {
                $updateData['date'] = Carbon::parse($validated['date'])->format('Y-m-d');
                $updateData['day_of_week'] = strtolower(Carbon::parse($validated['date'])->format('l'));
            } elseif (isset($validated['day_of_week'])) {
                $updateData['day_of_week'] = strtolower($validated['day_of_week']);
            }

            // Check for conflicts (excluding current record)
            if (isset($validated['division_id']) || isset($validated['teacher_id']) || 
                isset($validated['room_id']) || isset($validated['start_time']) || 
                isset($validated['end_time']) || isset($validated['date'])) {
                
                $conflicts = $this->checkConflicts(
                    $updateData['division_id'] ?? $timetable->division_id,
                    $updateData['teacher_id'] ?? $timetable->teacher_id,
                    $updateData['room_id'] ?? $timetable->room_id,
                    $updateData['date'] ?? $timetable->date?->format('Y-m-d'),
                    $updateData['day_of_week'] ?? $timetable->day_of_week,
                    $updateData['start_time'] ?? $timetable->start_time?->format('H:i'),
                    $updateData['end_time'] ?? $timetable->end_time?->format('H:i'),
                    $timetable->id
                );

                if ($conflicts['has_conflicts']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Schedule conflict detected',
                        'conflicts' => $conflicts['details'],
                    ], 422);
                }
            }

            // Update timetable
            DB::beginTransaction();
            
            $timetable->update($updateData);
            
            DB::commit();

            $timetable->load(['division', 'subject', 'teacher', 'room']);

            return response()->json([
                'success' => true,
                'message' => 'Timetable entry updated successfully',
                'data' => [
                    'id' => $timetable->id,
                    'division_name' => $timetable->division->division_name,
                    'subject_name' => $timetable->subject->name,
                    'teacher_name' => $timetable->teacher->name ?? 'N/A',
                    'day_name' => $timetable->day_name,
                    'date' => $timetable->date?->format('Y-m-d'),
                    'time' => $timetable->formatted_time_range,
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Timetable update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update timetable entry',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/timetables/{id}
     * Soft delete a timetable entry
     */
    public function destroy(Timetable $timetable): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $timetableId = $timetable->id;
            $timetable->delete(); // Soft delete
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Timetable entry deleted successfully',
                'data' => [
                    'id' => $timetableId,
                    'deleted' => true,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Timetable delete error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete timetable entry',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check for scheduling conflicts
     */
    private function checkConflicts(
        int $divisionId,
        int $teacherId,
        ?int $roomId,
        ?string $date,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): array {
        $conflicts = [];
        $hasConflicts = false;

        // Build base query
        $baseQuery = Timetable::where('status', 'active')
            ->where('is_break_time', false);

        if ($excludeId) {
            $baseQuery->where('id', '!=', $excludeId);
        }

        // Check division conflict
        $divisionConflict = (clone $baseQuery)
            ->where('division_id', $divisionId)
            ->where(function ($q) use ($date, $dayOfWeek) {
                if ($date) {
                    $q->whereDate('date', $date);
                } else {
                    $q->where('day_of_week', strtolower($dayOfWeek));
                }
            })
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                       ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        if ($divisionConflict) {
            $hasConflicts = true;
            $conflicts[] = [
                'type' => 'division',
                'message' => 'Division already has a class at this time',
            ];
        }

        // Check teacher conflict
        $teacherConflict = (clone $baseQuery)
            ->where('teacher_id', $teacherId)
            ->where(function ($q) use ($date, $dayOfWeek) {
                if ($date) {
                    $q->whereDate('date', $date);
                } else {
                    $q->where('day_of_week', strtolower($dayOfWeek));
                }
            })
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                       ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        if ($teacherConflict) {
            $hasConflicts = true;
            $conflicts[] = [
                'type' => 'teacher',
                'message' => 'Teacher is already scheduled for another class at this time',
            ];
        }

        // Check room conflict
        if ($roomId) {
            $roomConflict = (clone $baseQuery)
                ->where('room_id', $roomId)
                ->where(function ($q) use ($date, $dayOfWeek) {
                    if ($date) {
                        $q->whereDate('date', $date);
                    } else {
                        $q->where('day_of_week', strtolower($dayOfWeek));
                    }
                })
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->where(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<', $endTime)
                           ->where('end_time', '>', $startTime);
                    });
                })
                ->exists();

            if ($roomConflict) {
                $hasConflicts = true;
                $conflicts[] = [
                    'type' => 'room',
                    'message' => 'Room is already booked at this time',
                ];
            }
        }

        return [
            'has_conflicts' => $hasConflicts,
            'details' => $conflicts,
        ];
    }
}
