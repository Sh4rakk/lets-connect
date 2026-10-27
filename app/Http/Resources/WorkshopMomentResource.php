<?php
declare(strict_types=1);

namespace App\Http\Resources;

use SingleQuote\LaravelApiResource\Service\ApiPolicyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopMomentResource extends JsonResource
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
            'workshop_id' => $this->whenHas('workshop_id'),
            'moment_id' => $this->whenHas('moment_id'),
            'bookings_count' => $this->whenCounted('bookings'),
            // alleen als de workshop en het aantal boekingen geladen zijn
            'spots_left' => $this->when(
                $this->relationLoaded('workshop') && $this->workshop && isset($this->bookings_count),
                fn () => max(0, $this->workshop->capacity - $this->bookings_count)
            ),
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
            'workshop' => new \App\Http\Resources\WorkshopResource($this->whenLoaded('workshop')),
            'moment' => $this->whenLoaded('moment'),
            'bookings' => \App\Http\Resources\BookingsResource::collection($this->whenLoaded('bookings')),
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
