<?php

namespace App\Http\Requests\Users;

use App\Models\User;
use SingleQuote\LaravelApiResource\Service\ApiRequestService;
use Illuminate\Foundation\Http\FormRequest;

class ShowUserRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ... ApiRequestService::defaults(User::class),
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
            ... ApiRequestService::attributes(User::class),
        ];
    }
}
