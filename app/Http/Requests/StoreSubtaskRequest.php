<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Task;

class StoreSubtaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => ['required', Rule::in(['Low', 'Medium', 'High', 'Critical'])],
            'project_id' => 'required|exists:projects,id',
            'parent_task_id' => 'required|exists:tasks,id',
            'assigned_to' => 'required|exists:users,id',
            'status' => ['sometimes', Rule::in(['Not Started', 'In Progress', 'Completed'])],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $parentTaskId = $this->input('parent_task_id');
            $startTime = $this->input('start_time');
            $endTime = $this->input('end_time');
            if ($parentTaskId && $startTime && $endTime) {
                $parentTask = Task::find($parentTaskId);
                if ($parentTask) {
                    if ($startTime < $parentTask->start_time || $endTime > $parentTask->end_time) {
                        $validator->errors()->add('start_time', 'The subtask time range must be within the parent task time range.');
                        $validator->errors()->add('end_time', 'The subtask time range must be within the parent task time range.');
                    }
                }
            }
        });
    }
} 