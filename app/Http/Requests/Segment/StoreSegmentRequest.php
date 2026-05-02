<?php

namespace App\Http\Requests\Segment;

use Illuminate\Foundation\Http\FormRequest;

class StoreSegmentRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'rules' => ['nullable', 'string'],
            'prizes' => ['nullable', 'string'],
            'rulebook' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'submission_limit' => ['nullable', 'integer', 'min:1'],
            'max_teams' => ['nullable', 'integer', 'min:1'],
            'registration_opens_at' => ['nullable', 'date'],
            'registration_closes_at' => ['nullable', 'date', 'after_or_equal:registration_opens_at'],
            'submission_opens_at' => ['nullable', 'date'],
            'submission_closes_at' => ['nullable', 'date', 'after_or_equal:submission_opens_at'],
            'results_at' => ['nullable', 'date', 'after_or_equal:submission_closes_at'],
            'is_active' => ['nullable', 'boolean'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'order' => ['nullable', 'integer'],
        ];
    }
}
