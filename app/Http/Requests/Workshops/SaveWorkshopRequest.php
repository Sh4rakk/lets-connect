<?php

namespace App\Http\Requests\Workshops;

use App\Models\Moment;
use App\Models\Workshop;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveWorkshopRequest extends FormRequest
{
    private ?Collection $moments = null;

    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        $email = $this->input('leader_email');

        $this->merge([
            'leader_email' => is_string($email) ? (strtolower(trim($email)) ?: null) : null,
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('workshops', 'name')->ignore($this->workshopId())],
            'location' => ['nullable', 'string', 'max:255'],
            'full_description' => ['nullable', 'string', 'max:9999'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'leader_email' => ['nullable', 'email', 'max:255'],
            'rounds' => ['required', 'array'],
        ];

        foreach ($this->moments() as $moment) {
            $rules["rounds.{$moment->id}"] = ['nullable', 'array'];
            $rules["rounds.{$moment->id}.enabled"] = ['nullable', 'boolean'];
            $rules["rounds.{$moment->id}.capacity"] = [
                "exclude_unless:rounds.{$moment->id}.enabled,1",
                'required',
                'integer',
                'min:1',
                'max:999',
            ];
        }

        return $rules;
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->enabledRounds()->isEmpty()) {
                    $validator->errors()->add('rounds', 'Zet minstens 1 ronde aan.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vul een titel in.',
            'name.max' => 'De titel mag maximaal 255 tekens zijn.',
            'name.unique' => 'Er bestaat al een workshop met deze titel.',
            'location.max' => 'Het lokaal mag maximaal 255 tekens zijn.',
            'full_description.max' => 'De beschrijving mag maximaal 9999 tekens zijn.',
            'image.image' => 'De afbeelding moet een JPG of PNG zijn.',
            'image.mimes' => 'De afbeelding moet een JPG of PNG zijn.',
            'image.max' => 'De afbeelding mag maximaal 2 MB zijn.',
            'image.uploaded' => 'De afbeelding mag maximaal 2 MB zijn.',
            'leader_email.email' => 'Vul een geldig e-mailadres in.',
            'rounds.required' => 'Zet minstens 1 ronde aan.',
            'rounds.*.capacity.required' => 'Vul het aantal plekken in.',
            'rounds.*.capacity.integer' => 'Het aantal plekken moet een heel getal zijn.',
            'rounds.*.capacity.min' => 'Een ronde heeft minstens 1 plek nodig.',
            'rounds.*.capacity.max' => 'Een ronde mag maximaal 999 plekken hebben.',
        ];
    }

    public function moments(): Collection
    {
        return $this->moments ??= Moment::orderBy('id')->get();
    }

    public function enabledRounds(): Collection
    {
        return $this->moments()
            ->filter(fn (Moment $moment) => $this->boolean("rounds.{$moment->id}.enabled"))
            ->mapWithKeys(fn (Moment $moment) => [
                $moment->id => (int) $this->input("rounds.{$moment->id}.capacity"),
            ]);
    }

    private function workshopId(): ?string
    {
        $workshop = $this->route('workshop');

        return $workshop instanceof Workshop ? $workshop->id : null;
    }
}
