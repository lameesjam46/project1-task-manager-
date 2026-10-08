<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'project_id' => 'required|exists:projects,id',
            'status' => 'sometimes|in:Not Started,In Progress,Completed',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'due_date' => 'nullable|date|after_or_equal:start_time'
        ];
    }
}
