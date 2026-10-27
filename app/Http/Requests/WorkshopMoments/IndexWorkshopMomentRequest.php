<?php

namespace App\Http\Requests\WorkshopMoments;

use App\Models\WorkshopMoment;
use SingleQuote\LaravelApiResource\Service\ApiRequestService;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Concerns\SafeApiFilters;

class IndexWorkshopMomentRequest extends FormRequest
{
    use SafeApiFilters;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ... ApiRequestService::defaults(WorkshopMoment::class),
            ... $this->filterColumnRules(WorkshopMoment::class),
            ... $this->relationRulesForStudents(WorkshopMoment::class),
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
            ... ApiRequestService::attributes(WorkshopMoment::class),
        ];
    }
}
