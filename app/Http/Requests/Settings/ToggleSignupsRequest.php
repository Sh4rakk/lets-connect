<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class ToggleSignupsRequest extends FormRequest
{
    /**
     * Alleen admins mogen de inschrijvingen open of dicht zetten.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
