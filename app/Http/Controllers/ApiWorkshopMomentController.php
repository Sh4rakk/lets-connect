<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\WorkshopMoments\IndexWorkshopMomentsAction;
use App\Actions\WorkshopMoments\ShowWorkshopMomentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkshopMoments\IndexWorkshopMomentRequest;
use App\Http\Requests\WorkshopMoments\ShowWorkshopMomentRequest;
use App\Models\WorkshopMoment;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiWorkshopMomentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(WorkshopMoment::class);
    }

    /**
     * @param IndexWorkshopMomentRequest $request
     * @param IndexWorkshopMomentsAction $action
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(IndexWorkshopMomentRequest $request, IndexWorkshopMomentsAction $action): JsonResponse|AnonymousResourceCollection
    {
        return $action->api()->data()->handle($request);
    }

    /**
     * @param ShowWorkshopMomentRequest $request
     * @param WorkshopMoment $workshopMoment
     * @param ShowWorkshopMomentAction $action
     * @return JsonResponse
     */
    public function show(ShowWorkshopMomentRequest $request, WorkshopMoment $workshopMoment, ShowWorkshopMomentAction $action): JsonResponse
    {
        return $action->api()->handle($request, $workshopMoment);
    }
}
