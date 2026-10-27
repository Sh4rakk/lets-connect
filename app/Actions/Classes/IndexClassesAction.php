<?php
declare(strict_types=1);

namespace App\Actions\Classes;

use App\Http\Requests\Classes\IndexClassesRequest;
use App\Http\Resources\ClassesResource;
use SingleQuote\LaravelApiResource\Infra\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Classes;

final class IndexClassesAction extends Action
{
    /**
     * @param IndexClassesRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function handle(IndexClassesRequest $request): JsonResponse|AnonymousResourceCollection
    {
        /** @var Builder $query */
        $query = Classes::apiDefaults($request);

        $this->loadAdditionals($request, $query);

        $classes = $query->paginate($request->validated('limit', config('laravel-api-resource.api.limit', 1000)));

        return $this->withData(ClassesResource::collection($classes))->result();
    }

    /**
     * @param IndexClassesRequest $request
     * @param Builder $query
     * @return Builder
     */
    private function loadAdditionals(IndexClassesRequest $request, Builder &$query): Builder
    {
        if (!$request->hasAny(['orderBy', 'orderByDesc'])) {
            return $query->orderByDesc('created_at');
        }

        return $query;
    }
}
