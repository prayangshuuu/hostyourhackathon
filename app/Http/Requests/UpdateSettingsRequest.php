<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('settings.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'app_name' => ['nullable', 'string', 'max:100'],
            'app_url' => ['nullable', 'url'],
            'support_email' => ['nullable', 'email'],
            'app_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:1024'],
            'smtp_host' => ['nullable', 'string'],
            'smtp_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'smtp_username' => ['nullable', 'string'],
            'smtp_password' => ['nullable', 'string'],
            'smtp_encryption' => ['nullable', 'in:tls,ssl,none'],
            'mail_from_name' => ['nullable', 'string'],
            'mail_from_address' => ['nullable', 'email'],
            'max_file_upload_mb' => ['nullable', 'integer', 'min:1', 'max:100'],
            'allow_registration' => ['nullable'],
            'allow_multiple_hackathons' => ['nullable'],
            'enable_google_oauth' => ['nullable'],
            'enable_submissions' => ['nullable'],
            'enable_judging' => ['nullable'],
            'enable_leaderboard' => ['nullable'],
        ];
    }
}
