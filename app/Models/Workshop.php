<?php

namespace App\Models;

use App\Models\Concerns\HasQrToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use SingleQuote\LaravelApiResource\Traits\HasApi;

class Workshop extends Model
{
    use HasUuids, HasApi, HasQrToken;

    protected $fillable = [
        'name',
        'full_description',
        'image_url',
        'capacity',
        'location',
    ];

    protected $hidden = [
        'qr_token',
        'leader_id',
    ];

    protected $casts = [
        'is_manual' => 'boolean',
    ];

    public function workshopMoments(): HasMany
    {
        return $this->hasMany(WorkshopMoment::class);
    }

    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Bookings::class, WorkshopMoment::class, 'workshop_id', 'wm_id');
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }
}
