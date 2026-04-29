<?php

namespace App\Http\Requests\Hackathon;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHackathonRequest extends FormRequest
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
            'title'                   => ['required', 'string', 'max:255'],
            'tagline'                 => ['required', 'string', 'max:500'],
            'description'             => ['required', 'string'],
            'logo'                    => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'banner'                  => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:4096'],
            'primary_color'           => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'min_team_size'           => ['required', 'integer', 'min:1'],
            'max_team_size'           => ['required', 'integer', 'gte:min_team_size'],
            'allow_solo'              => ['required', 'boolean'],
            'registration_opens_at'   => ['nullable', 'date'],
            'registration_closes_at'  => ['nullable', 'date', 'after:registration_opens_at'],
            'submission_opens_at'     => ['nullable', 'date'],
            'submission_closes_at'    => ['nullable', 'date', 'after:submission_opens_at'],
            'results_at'              => ['nullable', 'date', 'after:submission_closes_at'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'primary_color.regex'          => 'The color must be a valid hex code (e.g. #6366f1).',
            'max_team_size.gte'            => 'Max team size must be greater than or equal to min team size.',
            'registration_closes_at.after' => 'Registration close must be after registration open.',
            'submission_closes_at.after'   => 'Submission close must be after submission open.',
            'results_at.after'             => 'Results date must be after submission close.',
        ];
    }
}
