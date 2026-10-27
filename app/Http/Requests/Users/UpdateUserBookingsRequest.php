<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserBookingsRequest extends FormRequest
{
    /**
     * Alleen admins mogen de workshops van een student aanpassen.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('updateBookings', $this->route('user'));
    }

    /**
     * Per ronde een workshop moment id, of leeg om de boeking te verwijderen.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rounds.1' => ['nullable', 'integer', 'exists:workshop_moments,id'],
            'rounds.2' => ['nullable', 'integer', 'exists:workshop_moments,id'],
            'rounds.3' => ['nullable', 'integer', 'exists:workshop_moments,id'],
        ];
    }
}
