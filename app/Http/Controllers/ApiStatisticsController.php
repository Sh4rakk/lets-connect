<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Statistics\ShowStatisticsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Statistics\ShowStatisticsRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiStatisticsController extends Controller
{
    /**
     * @param ShowStatisticsRequest $request
     * @param ShowStatisticsAction $action
     * @return JsonResponse
     */
    public function show(ShowStatisticsRequest $request, ShowStatisticsAction $action): JsonResponse
    {
        return $action->api()->handle();
    }
}
