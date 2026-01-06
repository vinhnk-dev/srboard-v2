<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ProjectRequest extends FormRequest
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
        $projectId = $this->route('id') ?? $this->input('id');
        return [
                "project_name" => ["required", "string", "max:255"],
                "project_code" => ["required", "string", "max:50",Rule::unique('projects', 'project_code')->ignore($projectId)],
                "project_type" => ["required"],
                "active" => ["boolean"],
                "git_url" => ["required",Rule::unique('projects', 'git_url')->ignore($projectId)],
                "description" => ["required", "string"],
                "url" => ["required", Rule::unique('projects', 'url')->ignore($projectId)],
                "group_assignment_id" => [""],
                "status_id" => [""],
                "status_id.*" => ["exists:statuses,id"],
                "show" => [""],
            ];
    }
}
