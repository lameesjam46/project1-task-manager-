<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'phone'=>'sometimes|string|max:15',
//            'user_id'=>'sometime|exists:user,id',
            'Image'=>'sometimes|image|mimes:png,jpg,jpeg,gif|max:2048',
            'address'=>'sometimes|string',
            'date_of_birth'=>'sometimes|date',
            'bio'=>'sometimes|string'
        ];
    }
}
