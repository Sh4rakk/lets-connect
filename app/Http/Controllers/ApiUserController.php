<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Users\IndexUsersAction;
use App\Actions\Users\ShowUserAction;
use App\Actions\Users\UpdateUserBookingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\IndexUserRequest;
use App\Http\Requests\Users\ShowUserRequest;
use App\Http\Requests\Users\UpdateUserBookingsRequest;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiUserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    /**
     * @param IndexUserRequest $request
     * @param IndexUsersAction $action
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(IndexUserRequest $request, IndexUsersAction $action): JsonResponse|AnonymousResourceCollection
    {
        return $action->api()->data()->handle($request);
    }

    /**
     * @param ShowUserRequest $request
     * @param User $user
     * @param ShowUserAction $action
     * @return JsonResponse
     */
    public function show(ShowUserRequest $request, User $user, ShowUserAction $action): JsonResponse
    {
        return $action->api()->handle($request, $user);
    }

    /**
     * @param UpdateUserBookingsRequest $request
     * @param User $user
     * @param UpdateUserBookingsAction $action
     * @return JsonResponse
     */
    public function updateBookings(UpdateUserBookingsRequest $request, User $user, UpdateUserBookingsAction $action): JsonResponse
    {
        return $action->api()->handle($request, $user);
    }
}
