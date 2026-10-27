<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Workshops\IndexWorkshopsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workshops\IndexWorkshopRequest;
use App\Models\Workshop;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiWorkshopController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Workshop::class);
    }

    /**
     * @param IndexWorkshopRequest $request
     * @param IndexWorkshopsAction $action
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(IndexWorkshopRequest $request, IndexWorkshopsAction $action): JsonResponse|AnonymousResourceCollection
    {
        return $action->api()->data()->handle($request);
    }
}
