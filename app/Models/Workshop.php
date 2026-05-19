<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\DB;


class Workshop extends Model
{
    use HasUuids;

    public $guarded = [];

    public function workshopMoments(): HasMany
    {
        return $this->hasMany(WorkshopMoment::class);
    }

    public function bookings()
    {
        return $this->hasManyThrough(Bookings::class, WorkshopMoment::class, 'workshop_id', 'wm_id');
    }
}
