<?php

namespace App\Http\Requests\Statistics;

use Illuminate\Foundation\Http\FormRequest;

class ShowStatisticsRequest extends FormRequest
{
    /**
     * Alleen admins mogen de statistieken zien.
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
