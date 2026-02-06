<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
    public function rules()
    {
        $userId = $this->route('id') ?? $this->input('id');
        return [
            "username" => ["required", "string", "max:255",Rule::unique('users')->ignore($userId)],
            "password" => ["nullable", "string", "min:8"],
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "email", Rule::unique('users')->ignore($userId)],
            "active" => ["nullable"],
            "user_group_id" => ["nullable", "array"],
            "user_group_id.*" => ["exists:groups,id"],
            "role" => ["required"],
            "avatar" => ["nullable", "image", "max:2048"],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'active' => $this->boolean('active'),
        ]);
    }

}
