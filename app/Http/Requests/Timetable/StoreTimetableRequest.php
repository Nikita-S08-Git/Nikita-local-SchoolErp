<?php

namespace App\Http\Requests\Timetable;

use App\Http\Requests\BaseFormRequest;
use App\Models\Academic\Timetable;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

/**
 * Form Request for creating/updating Timetable entries
 * 
 * Handles validation with custom conflict detection messages
 * and prevents overlapping time ranges.
 * 
 * @package App\Http\Requests\Timetable
 */
class StoreTimetableRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user has appropriate role
        return $this->user()->hasAnyRole(['admin', 'principal', 'teacher']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $timetableId = $this->route('timetable')?->id;

        return [
            // Basic required fields
            'division_id' => [
                'required',
                'integer',
                'exists:divisions,id',
            ],
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'teacher_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'day_of_week' => [
                'required_without:date',
                Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']),
            ],
            
            // Date - optional for specific date classes
            'date' => [
                'nullable',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    $date = \Carbon\Carbon::parse($value);
                    if ($date->dayOfWeek === \Carbon\Carbon::SUNDAY) {
                        $fail('Sundays are weekly off days. Cannot schedule classes on Sundays.');
                    }
                },
            ],
            
            // Time fields - can be either time_slot_id or direct time input
            'time_slot_id' => [
                'required_without:start_time,end_time',
                'integer',
                'exists:time_slots,id',
            ],
            'start_time' => [
                'required_without:time_slot_id',
                'date_format:H:i',
            ],
            'end_time' => [
                'required_without:time_slot_id',
                'date_format:H:i',
                'after:start_time',
            ],
            
            // Optional fields
            'period_name' => [
                'nullable',
                'string',
                'max:50',
            ],
            'room_id' => [
                'nullable',
                'integer',
                'exists:rooms,id',
            ],
            'room_number' => [
                'nullable',
                'string',
                'max:50',
            ],
            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',
            ],
            'status' => [
                'nullable',
                Rule::in(['active', 'cancelled', 'completed']),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Required field messages
            'division_id.required' => 'Please select a division/class.',
            'subject_id.required' => 'Please select a subject.',
            'teacher_id.required' => 'Please select a teacher.',
            'day_of_week.required_without' => 'Please select a day of the week or specify a date.',
            'time_slot_id.required' => 'Please select a time slot.',
            'academic_year_id.required' => 'Please select an academic year.',

            // Validation messages
            'day_of_week.in' => 'Please select a valid day (Monday to Saturday).',
            'day_of_week.required_without' => 'Please select a day of the week or a specific date.',
            'date.after_or_equal' => 'Date cannot be in the past.',
            'start_time.date_format' => 'Start time must be in HH:MM format (e.g., 09:00).',
            'end_time.date_format' => 'End time must be in HH:MM format (e.g., 10:00).',
            'end_time.after' => 'End time must be after start time.',

            // Custom conflict messages (added dynamically in controller)
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Get the time values
            $startTime = $this->getStartTime();
            $endTime = $this->getEndTime();

            if (!$startTime || !$endTime) {
                return;
            }

            $divisionId = $this->division_id;
            $teacherId = $this->teacher_id;
            $roomId = $this->room_id;
            $dayOfWeek = $this->day_of_week;
            $date = $this->date;
            $timetableId = $this->route('timetable')?->id;

            // If specific date is provided, check for date-specific conflicts
            if ($date) {
                // Check division conflict for specific date
                $dateDivisionConflict = Timetable::checkDateDivisionConflict(
                    $divisionId,
                    $date,
                    $startTime,
                    $endTime,
                    $timetableId
                );

                if ($dateDivisionConflict) {
                    $validator->errors()->add(
                        'division_id',
                        'This division already has a class scheduled on ' . Carbon\Carbon::parse($date)->format('l, F d, Y') . ' at this time.'
                    );
                }

                // Check teacher conflict for specific date
                $dateTeacherConflict = Timetable::checkTeacherDateConflict(
                    $teacherId,
                    $date,
                    $startTime,
                    $endTime,
                    $timetableId
                );

                if ($dateTeacherConflict) {
                    $validator->errors()->add(
                        'teacher_id',
                        'This teacher is already assigned to another class on ' . Carbon\Carbon::parse($date)->format('l, F d, Y') . ' at this time.'
                    );
                }

                // Check room conflict for specific date
                if ($roomId) {
                    $dateRoomConflict = Timetable::checkRoomDateConflict(
                        $roomId,
                        $date,
                        $startTime,
                        $endTime,
                        $timetableId
                    );

                    if ($dateRoomConflict) {
                        $validator->errors()->add(
                            'room_id',
                            'This room is already booked for another class on ' . Carbon\Carbon::parse($date)->format('l, F d, Y') . ' at this time.'
                        );
                    }
                }
            }

            // Check for regular day_of_week conflict (for recurring weekly schedule)
            if ($dayOfWeek) {
                // Check for division conflict (same division, same day, overlapping time)
                $divisionConflict = Timetable::checkDivisionConflict(
                    $divisionId,
                    $dayOfWeek,
                    $startTime,
                    $endTime,
                    $timetableId
                );

                if ($divisionConflict) {
                    $validator->errors()->add(
                        'division_id',
                        'This division already has a class scheduled at this time on ' . ucfirst($dayOfWeek) . '.'
                    );
                }

                // Check for teacher conflict
                $teacherConflict = Timetable::checkTeacherConflict(
                    $teacherId,
                    $dayOfWeek,
                    $startTime,
                    $endTime,
                    $timetableId
                );

                if ($teacherConflict) {
                    $validator->errors()->add(
                        'teacher_id',
                        'This teacher is already assigned to another class at this time on ' . ucfirst($dayOfWeek) . '.'
                    );
                }

                // Check for room conflict
                if ($roomId) {
                    $roomConflict = Timetable::checkRoomConflict(
                        $roomId,
                        $dayOfWeek,
                        $startTime,
                        $endTime,
                        $timetableId
                    );

                    if ($roomConflict) {
                        $validator->errors()->add(
                            'room_id',
                            'This room is already booked for another class at this time on ' . ucfirst($dayOfWeek) . '.'
                        );
                    }
                }
            }
        });
    }

    /**
     * Get start time from either time_slot_id or direct input
     *
     * @return string|null
     */
    protected function getStartTime(): ?string
    {
        if ($this->filled('time_slot_id')) {
            $timeSlot = \App\Models\Academic\TimeSlot::find($this->time_slot_id);
            return $timeSlot?->start_time;
        }

        return $this->start_time;
    }

    /**
     * Get end time from either time_slot_id or direct input
     *
     * @return string|null
     */
    protected function getEndTime(): ?string
    {
        if ($this->filled('time_slot_id')) {
            $timeSlot = \App\Models\Academic\TimeSlot::find($this->time_slot_id);
            return $timeSlot?->end_time;
        }

        return $this->end_time;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // If time_slot_id is provided, fetch the times
        if ($this->filled('time_slot_id')) {
            $timeSlot = \App\Models\Academic\TimeSlot::find($this->time_slot_id);
            if ($timeSlot) {
                $this->merge([
                    'start_time' => $timeSlot->start_time,
                    'end_time' => $timeSlot->end_time,
                ]);
            }
        }
    }
}
