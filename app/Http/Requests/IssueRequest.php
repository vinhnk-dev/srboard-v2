<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueRequest extends FormRequest
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
            "title" => ["required", "string", "max:255"],
            "status" => ["required", "integer"],
            "url" => ["required"],
            "issue_description" => ["required", "string"],
            "due_date" => ["required"],
            "project_id" => ["required", "integer"],
            "user_assign" => [""],
            "report_assign" => [""],
            'pic_url' => ['nullable', 'array'],
            'pic_url.*' => ['string'],

            'picture_url' => ['nullable', 'array'],
            'picture_url.*' => [
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120', 
            ],
        ];
    }
}
