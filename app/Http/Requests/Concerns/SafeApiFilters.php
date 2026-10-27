<?php

namespace App\Http\Requests\Concerns;

use App\Rules\OnlyAllowedColumns;
use SingleQuote\LaravelApiResource\Rules\OrderByRule;

/**
 * Extra regels bovenop ApiRequestService::defaults() van het package.
 * Gebruik deze in elke Index request.
 */
trait SafeApiFilters
{
    /**
     * Alleen filteren op de eigen kolommen van het model.
     *
     * @return array<string, mixed>
     */
    protected function filterColumnRules(string $modelClass): array
    {
        $rule = new OnlyAllowedColumns($modelClass);

        return [
            'where' => ['nullable', 'array', $rule],
            'orWhere' => ['nullable', 'array', $rule],
            'whereIn' => ['nullable', 'array', $rule],
            'whereNotIn' => ['nullable', 'array', $rule],
            'whereJsonContains' => ['nullable', 'array', $rule],
            'whereJsonDoesntContain' => ['nullable', 'array', $rule],
        ];
    }

    /**
     * Studenten mogen geen relaties laden of daarop filteren,
     * anders kunnen ze via ?with[bookings.student] de namen van andere studenten opvragen.
     *
     * @return array<string, mixed>
     */
    protected function relationRulesForStudents(string $modelClass): array
    {
        if ($this->user()?->hasRole('admin')) {
            return [];
        }

        return [
            // sorteren mag wel, maar niet op een kolom van een relatie (geen punt in de naam)
            'orderBy' => ['nullable', 'string', 'not_regex:/\./', new OrderByRule($modelClass)],
            'orderByDesc' => ['nullable', 'string', 'not_regex:/\./', new OrderByRule($modelClass)],
            'with' => 'prohibited',
            'withCount' => 'prohibited',
            'has' => 'prohibited',
            'doesntHave' => 'prohibited',
            'whereRelation' => 'prohibited',
        ];
    }
}
