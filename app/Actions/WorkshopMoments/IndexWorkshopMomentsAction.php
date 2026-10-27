<?php
declare(strict_types=1);

namespace App\Actions\WorkshopMoments;

use App\Http\Requests\WorkshopMoments\IndexWorkshopMomentRequest;
use App\Http\Resources\WorkshopMomentResource;
use SingleQuote\LaravelApiResource\Infra\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\WorkshopMoment;

final class IndexWorkshopMomentsAction extends Action
{
    /**
     * @param IndexWorkshopMomentRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function handle(IndexWorkshopMomentRequest $request): JsonResponse|AnonymousResourceCollection
    {
        /** @var Builder $query */
        $query = WorkshopMoment::apiDefaults($request);

        $this->loadAdditionals($request, $query);

        $workshopmoments = $query->paginate($request->validated('limit', config('laravel-api-resource.api.limit', 1000)));

        return $this->withData(WorkshopMomentResource::collection($workshopmoments))->result();
    }

    /**
     * @param IndexWorkshopMomentRequest $request
     * @param Builder $query
     * @return Builder
     */
    private function loadAdditionals(IndexWorkshopMomentRequest $request, Builder &$query): Builder
    {
        // altijd de capaciteit en het aantal boekingen meesturen, daar is deze lijst voor
        $query->with('workshop:id,name,capacity')->withCount('bookings');

        if (!$request->hasAny(['orderBy', 'orderByDesc'])) {
            return $query->orderByDesc('created_at');
        }

        return $query;
    }
}
