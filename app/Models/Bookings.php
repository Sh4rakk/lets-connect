<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SingleQuote\LaravelApiResource\Traits\HasApi;

class Bookings extends Model
{
    use HasFactory, HasApi;

    protected $fillable = [
        'wm_id',
        'student_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, "student_id");
    }

    public function workshopMoments(): BelongsTo
    {
        return $this->belongsTo(WorkshopMoment::class, "wm_id");
    }
}
