<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Settings\ToggleSignupsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ToggleSignupsRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiSettingController extends Controller
{
    /**
     * @param ToggleSignupsRequest $request
     * @param ToggleSignupsAction $action
     * @return JsonResponse
     */
    public function toggleSignups(ToggleSignupsRequest $request, ToggleSignupsAction $action): JsonResponse
    {
        return $action->api()->handle();
    }
}
