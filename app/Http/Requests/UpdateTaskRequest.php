<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->task->user_id === auth()->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->method();

        if ($method == 'PUT') {
            return [
                'name' => ['required', 'string', 'max:100'],
                'content' => ['required', 'string', 'max:255']
            ];
        }

        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'content' => ['sometimes', 'required', 'string', 'max:255']
        ];   
    }
}
