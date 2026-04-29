<?php

namespace App\Http\Requests\Submission;

use Illuminate\Foundation\Http\FormRequest;

class FinalizeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * No additional fields needed — authorization is handled in the service.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
