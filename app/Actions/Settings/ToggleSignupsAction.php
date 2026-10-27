<?php

declare(strict_types=1);

namespace App\Actions\Settings;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use SingleQuote\LaravelApiResource\Infra\Action;

final class ToggleSignupsAction extends Action
{
    /**
     * Zet de inschrijvingen open als ze dicht zijn en andersom.
     *
     * @return JsonResponse|RedirectResponse
     */
    public function handle(): JsonResponse|RedirectResponse
    {
        $setting = Setting::firstOrCreate(['key' => 'signups_open'], ['value' => '0']);

        $setting->update(['value' => $setting->value === '1' ? '0' : '1']);

        return $this->withData(['signups_open' => $setting->value === '1'])->result();
    }
}
