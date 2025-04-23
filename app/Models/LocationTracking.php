<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationTracking extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'location_tracking';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'command_id',
        'pickup_latitude',
        'pickup_longitude',
        'delivery_latitude',
        'delivery_longitude',
        'livreur_latitude',
        'livreur_longitude',
        'delivery_route',
        'estimated_delivery_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pickup_latitude' => 'decimal:7',
        'pickup_longitude' => 'decimal:7',
        'delivery_latitude' => 'decimal:7',
        'delivery_longitude' => 'decimal:7',
        'livreur_latitude' => 'decimal:7',
        'livreur_longitude' => 'decimal:7',
        'delivery_route' => 'array',
        'estimated_delivery_time' => 'integer',
    ];

    /**
     * Get the command that owns the location tracking.
     */
    public function command(): BelongsTo
    {
        return $this->belongsTo(Command::class);
    }
}
