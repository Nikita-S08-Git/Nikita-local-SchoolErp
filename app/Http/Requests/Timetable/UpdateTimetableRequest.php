<?php

namespace App\Http\Requests\Timetable;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for updating Timetable entries
 * 
 * Extends StoreTimetableRequest to reuse validation rules
 * 
 * @package App\Http\Requests\Timetable
 */
class UpdateTimetableRequest extends StoreTimetableRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return parent::authorize();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $timetableId = $this->route('timetable')?->id;

        return array_merge(parent::rules(), [
            // Make fields optional for update
            'division_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:divisions,id',
            ],
            'subject_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'teacher_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:users,id',
            ],
        ]);
    }
}
