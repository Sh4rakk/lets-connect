<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Bookings\StoreBookingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bookings\StoreBookingsRequest;
use App\Models\Bookings;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiBookingsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Bookings::class);
    }

    /**
     * @param StoreBookingsRequest $request
     * @param StoreBookingsAction $action
     * @return JsonResponse
     */
    public function store(StoreBookingsRequest $request, StoreBookingsAction $action): JsonResponse
    {
        return $action->api()->handle($request);
    }
}
