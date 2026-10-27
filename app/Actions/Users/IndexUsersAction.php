<?php
declare(strict_types=1);

namespace App\Actions\Users;

use App\Http\Requests\Users\IndexUserRequest;
use App\Http\Resources\UserResource;
use SingleQuote\LaravelApiResource\Infra\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\User;

final class IndexUsersAction extends Action
{
    /**
     * @param IndexUserRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function handle(IndexUserRequest $request): JsonResponse|AnonymousResourceCollection
    {
        /** @var Builder $query */
        $query = User::apiDefaults($request);

        $this->loadAdditionals($request, $query);

        $users = $query->paginate($request->validated('limit', config('laravel-api-resource.api.limit', 1000)));

        return $this->withData(UserResource::collection($users))->result();
    }

    /**
     * @param IndexUserRequest $request
     * @param Builder $query
     * @return Builder
     */
    private function loadAdditionals(IndexUserRequest $request, Builder &$query): Builder
    {
        if (!$request->hasAny(['orderBy', 'orderByDesc'])) {
            return $query->orderByDesc('created_at');
        }

        return $query;
    }
}
