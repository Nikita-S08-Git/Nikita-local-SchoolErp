<?php

namespace App\Models\Result;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarksDraft extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'marks_drafts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'teacher_id',
        'exam_id',
        'subject_id',
        'student_id',
        'marks',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'marks' => 'decimal:2',
    ];

    /**
     * Get the teacher who created this draft.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the examination this draft belongs to.
     */
    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examination::class, 'exam_id');
    }

    /**
     * Get the subject this draft belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the student this draft belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\Student::class, 'student_id');
    }

    /**
     * Save or update a draft for a specific student.
     *
     * @param int $teacherId
     * @param int $examId
     * @param int $subjectId
     * @param int $studentId
     * @param float|null $marks
     * @param string|null $remarks
     * @return MarksDraft
     */
    public static function saveDraft(
        int $teacherId,
        int $examId,
        int $subjectId,
        int $studentId,
        ?float $marks = null,
        ?string $remarks = null
    ): MarksDraft {
        return self::updateOrCreate(
            [
                'teacher_id' => $teacherId,
                'exam_id' => $examId,
                'subject_id' => $subjectId,
                'student_id' => $studentId,
            ],
            [
                'marks' => $marks,
                'remarks' => $remarks,
            ]
        );
    }

    /**
     * Save multiple drafts at once.
     *
     * @param int $teacherId
     * @param int $examId
     * @param int $subjectId
     * @param array $marksData Array of ['student_id' => ['marks' => x, 'remarks' => y]]
     * @return array
     */
    public static function saveMultipleDrafts(
        int $teacherId,
        int $examId,
        int $subjectId,
        array $marksData
    ): array {
        $saved = [];
        $failed = [];

        foreach ($marksData as $studentId => $data) {
            try {
                $draft = self::saveDraft(
                    $teacherId,
                    $examId,
                    $subjectId,
                    $studentId,
                    $data['marks'] ?? null,
                    $data['remarks'] ?? null
                );
                $saved[] = $draft;
            } catch (\Exception $e) {
                $failed[] = [
                    'student_id' => $studentId,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'saved' => $saved,
            'failed' => $failed,
            'total' => count($marksData),
            'saved_count' => count($saved),
            'failed_count' => count($failed),
        ];
    }

    /**
     * Get drafts for a specific exam, subject, and teacher.
     *
     * @param int $teacherId
     * @param int $examId
     * @param int $subjectId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getDraftsForExam(
        int $teacherId,
        int $examId,
        int $subjectId
    ) {
        return self::where('teacher_id', $teacherId)
            ->where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->get();
    }

    /**
     * Get drafts keyed by student_id for easy lookup.
     *
     * @param int $teacherId
     * @param int $examId
     * @param int $subjectId
     * @return array
     */
    public static function getDraftsKeyedByStudent(
        int $teacherId,
        int $examId,
        int $subjectId
    ): array {
        return self::where('teacher_id', $teacherId)
            ->where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->get()
            ->keyBy('student_id')
            ->toArray();
    }

    /**
     * Clear drafts for a specific exam and subject.
     *
     * @param int $teacherId
     * @param int $examId
     * @param int|null $subjectId
     * @return int
     */
    public static function clearDrafts(
        int $teacherId,
        int $examId,
        ?int $subjectId = null
    ): int {
        $query = self::where('teacher_id', $teacherId)
            ->where('exam_id', $examId);

        if ($subjectId !== null) {
            $query->where('subject_id', $subjectId);
        }

        return $query->delete();
    }
}
