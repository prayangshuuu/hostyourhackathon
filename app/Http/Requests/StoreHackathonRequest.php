<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHackathonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'primary_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'min_team_size' => ['required', 'integer', 'min:1'],
            'max_team_size' => ['required', 'integer', 'gte:min_team_size'],
            'allow_solo' => ['boolean'],
            'registration_opens_at' => ['required', 'date'],
            'registration_closes_at' => ['required', 'date', 'after:registration_opens_at'],
            'submission_opens_at' => ['required', 'date', 'after:registration_opens_at'],
            'submission_closes_at' => ['required', 'date', 'after:submission_opens_at'],
            'results_at' => ['nullable', 'date', 'after:submission_closes_at'],
        ];
    }
}
