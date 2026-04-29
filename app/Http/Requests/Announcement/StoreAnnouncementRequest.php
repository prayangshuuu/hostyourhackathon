<?php

namespace App\Http\Requests\Announcement;

use App\Enums\AnnouncementVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnnouncementRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'visibility' => ['required', Rule::in(AnnouncementVisibility::values())],
            'segment_id' => ['nullable', 'required_if:visibility,segment', 'integer', 'exists:segments,id'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'segment_id' => 'segment',
            'scheduled_at' => 'scheduled date',
        ];
    }
}
