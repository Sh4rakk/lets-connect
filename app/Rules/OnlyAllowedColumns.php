<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use SingleQuote\LaravelApiResource\Infra\ApiModel;

/**
 * Controleert dat er alleen op toegestane kolommen gefilterd wordt.
 *
 * De package controleert dit zelf niet (de check in ValidateArrayKeys staat uit),
 * waardoor je op elke kolom kon filteren, ook verborgen kolommen zoals login_code_hash
 * en kolommen van andere tabellen (bijvoorbeeld bookings.student.email).
 */
class OnlyAllowedColumns implements ValidationRule
{
    private array $allowedColumns;

    public function __construct(string $modelClass)
    {
        // fillable + id/created_at/updated_at, zonder de $hidden kolommen
        $this->allowedColumns = ApiModel::fillable(new $modelClass())->values()->all();
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            return;
        }

        foreach ($this->findColumns($value) as $column) {
            if (!in_array($column, $this->allowedColumns, true)) {
                $fail("Er mag niet gefilterd worden op de kolom '$column'.");
                return;
            }
        }
    }

    /**
     * Haalt alle kolomnamen op, ook uit $or / $and groepen en lijstjes.
     */
    private function findColumns(array $conditions): array
    {
        $columns = [];

        foreach ($conditions as $key => $condition) {
            if (is_int($key) || $key === '$or' || $key === '$and') {
                $columns = [...$columns, ...$this->findColumns((array) $condition)];
                continue;
            }

            $columns[] = $key;
        }

        return $columns;
    }
}
