<?php

declare(strict_types=1);

namespace App\Actions\WorkshopMoments;

use App\Http\Requests\WorkshopMoments\ShowWorkshopMomentRequest;
use App\Http\Resources\WorkshopMomentResource;
use SingleQuote\LaravelApiResource\Infra\Action;
use App\Models\WorkshopMoment;
use Illuminate\Http\JsonResponse;

final class ShowWorkshopMomentAction extends Action
{
    /**
     * @param ShowWorkshopMomentRequest $request
     * @param WorkshopMoment $workshopMoment
     * @return JsonResponse
     */
    public function handle(ShowWorkshopMomentRequest $request, WorkshopMoment $workshopMoment): JsonResponse
    {
        $workshopMoment->modelDefaults($request);

        return $this->withData(new WorkshopMomentResource($workshopMoment))->result();
    }
}
