<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class JoinTeamRequest extends FormRequest
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
        // No additional input needed — the invite code comes from the route parameter.
        return [];
    }
}
