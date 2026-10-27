<?php
declare(strict_types=1);

namespace App\Http\Resources;

use SingleQuote\LaravelApiResource\Service\ApiPolicyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->whenHas('name'),
            'email' => $this->whenHas('email'),
            'class' => $this->whenHas('class'),
            'last_login_at' => $this->whenHas('last_login_at'),
            'email_verified_at' => $this->whenHas('email_verified_at'),
            'bookings_count' => $this->whenCounted('bookings'),
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
