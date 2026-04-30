<?php

namespace App\Http\Requests\Submission;

use Illuminate\Foundation\Http\FormRequest;

class SaveSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'problem_statement' => ['required', 'string'],
            'description' => ['required', 'string'],
            'tech_stack' => ['nullable', 'string'],
            'demo_url' => ['nullable', 'url', 'max:500'],
            'repo_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Your submission needs a title.',
            'problem_statement.required' => 'Describe the problem you are solving.',
            'description.required' => 'Provide a description of your project.',
            'demo_url.url' => 'Please enter a valid URL for the demo.',
            'repo_url.url' => 'Please enter a valid URL for the repository.',
        ];
    }
}
