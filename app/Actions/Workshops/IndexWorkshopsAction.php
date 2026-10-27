<?php
declare(strict_types=1);

namespace App\Actions\Workshops;

use App\Http\Requests\Workshops\IndexWorkshopRequest;
use App\Http\Resources\WorkshopResource;
use SingleQuote\LaravelApiResource\Infra\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Workshop;

final class IndexWorkshopsAction extends Action
{
    /**
     * @param IndexWorkshopRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function handle(IndexWorkshopRequest $request): JsonResponse|AnonymousResourceCollection
    {
        /** @var Builder $query */
        $query = Workshop::apiDefaults($request);

        $this->loadAdditionals($request, $query);

        $workshops = $query->paginate($request->validated('limit', config('laravel-api-resource.api.limit', 1000)));

        return $this->withData(WorkshopResource::collection($workshops))->result();
    }

    /**
     * @param IndexWorkshopRequest $request
     * @param Builder $query
     * @return Builder
     */
    private function loadAdditionals(IndexWorkshopRequest $request, Builder &$query): Builder
    {
        if (!$request->hasAny(['orderBy', 'orderByDesc'])) {
            return $query->orderByDesc('created_at');
        }

        return $query;
    }
}
