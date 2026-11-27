<?php

namespace App\Models;

use App\Models\Concerns\HasQrToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SingleQuote\LaravelApiResource\Traits\HasApi;

class WorkshopMoment extends Model
{
    use HasApi, HasQrToken;

    protected $fillable = [
        'workshop_id',
        'moment_id',
        'capacity',
    ];

    protected $hidden = [
        'qr_token',
    ];

    public function maxSpots(): int
    {
        return (int) ($this->capacity ?? $this->workshop?->capacity);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Bookings::class, 'wm_id');
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class, "workshop_id");
    }

    public function moment(): BelongsTo
    {
        return $this->belongsTo(Moment::class, "moment_id");
    }
}
