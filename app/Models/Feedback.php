<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    public const SCALE = [
        'niet' => 'Niet',
        'beetje' => 'Beetje',
        'heel_erg' => 'Heel erg',
    ];

    protected $table = 'feedback';

    protected $fillable = [
        'workshop_moment_id',
        'rating',
        'good',
        'better',
        'learned',
        'fun',
        'recommend',
        'step_token',
    ];

    protected $hidden = [
        'step_token',
    ];

    protected $casts = [
        'rating' => 'integer',
        'recommend' => 'boolean',
    ];

    public function workshopMoment(): BelongsTo
    {
        return $this->belongsTo(WorkshopMoment::class);
    }
}
