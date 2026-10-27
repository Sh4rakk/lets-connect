<?php
declare(strict_types=1);

namespace App\Http\Resources;

use SingleQuote\LaravelApiResource\Service\ApiPolicyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenHas('id'),
            'created_at' => $this->whenHas('created_at'),
            'updated_at' => $this->whenHas('updated_at'),
            'wm_id' => $this->whenHas('wm_id'),
            'student_id' => $this->whenHas('student_id'),
            ... $this->getRelations(),
            ... $this->getSearch($request),
            ... $this->getParse($request->get('parse', [])),
        ];
    }

    /**
     * @return array
     */
    private function getRelations(): array
    {
        return [
            'workshopMoments' => new \App\Http\Resources\WorkshopMomentResource($this->whenLoaded('workshopMoments')),
            'student' => new \App\Http\Resources\UserResource($this->whenLoaded('student')),
        ];
    }

    /**
     * @param array $parse
     * @return array
     */
    private function getParse(array $parse): array
    {
        $resources = [];

        if (in_array('policies', $parse)) {
            $resources['policies'] = ApiPolicyService::defaults($this->resource);
        }

        return $resources;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getSearch(Request $request): array
    {
        $resources = [];

        if ($request->filled('search')) {
            $resources['search'] = $this->name;
        }

        return $resources;
    }
}
