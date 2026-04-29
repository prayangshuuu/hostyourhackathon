<?php

namespace App\Http\Requests\Hackathon;

use Illuminate\Foundation\Http\FormRequest;

class InviteOrganizerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('hackathon'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.exists' => 'No registered user found with that email address.',
        ];
    }
}
