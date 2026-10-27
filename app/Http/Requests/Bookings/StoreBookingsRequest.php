<?php

namespace App\Http\Requests\Bookings;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Een student kiest per ronde (1, 2 en 3) een workshop.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rounds' => ['required', 'array:1,2,3'],
            'rounds.1' => ['required', 'uuid', 'exists:workshops,id'],
            'rounds.2' => ['required', 'uuid', 'exists:workshops,id'],
            'rounds.3' => ['required', 'uuid', 'exists:workshops,id'],
        ];
    }

    /**
     * Foutmeldingen in het Nederlands, de student ziet deze in een popup.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rounds.required' => 'Kies voor elke ronde een workshop.',
            'rounds.*.required' => 'Kies een workshop voor :attribute.',
            'rounds.*.uuid' => 'De gekozen workshop voor :attribute bestaat niet.',
            'rounds.*.exists' => 'De gekozen workshop voor :attribute bestaat niet.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'rounds.1' => 'ronde 1',
            'rounds.2' => 'ronde 2',
            'rounds.3' => 'ronde 3',
        ];
    }
}
