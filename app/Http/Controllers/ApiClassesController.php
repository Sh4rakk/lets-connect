<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Classes\IndexClassesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Classes\IndexClassesRequest;
use App\Models\Classes;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiClassesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Classes::class);
    }

    /**
     * @param IndexClassesRequest $request
     * @param IndexClassesAction $action
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(IndexClassesRequest $request, IndexClassesAction $action): JsonResponse|AnonymousResourceCollection
    {
        return $action->api()->data()->handle($request);
    }
}
