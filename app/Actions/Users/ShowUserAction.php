<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Http\Requests\Users\ShowUserRequest;
use App\Http\Resources\UserResource;
use SingleQuote\LaravelApiResource\Infra\Action;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class ShowUserAction extends Action
{
    /**
     * @param ShowUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function handle(ShowUserRequest $request, User $user): JsonResponse
    {
        $user->modelDefaults($request);

        return $this->withData(new UserResource($user))->result();
    }
}
