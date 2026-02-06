<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
        return [
            "name" => ["required", "string", "max:255"],
            'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'required_with:password_confirmation',
                ],
            'password_confirmation' => [
                'required_with:password',
                'same:password',
            ],
            'email' => ['required', 'email', 'unique:users,email,' . $this->id],
            "avatar" => ["nullable", "image", "max:2048"],
        ];
    }

    protected function passedValidation()
    {
        if ($this->hasFile('avatar')) {
            $this->merge([
                'avatar' => $this->file('avatar'),
            ]);
        }
    }

}
