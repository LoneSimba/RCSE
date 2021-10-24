<?php

namespace App\Models;

use App\Traits\Models\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Laravel\Jetstream\Team as JetstreamTeam;
use Laravel\Jetstream\Events\{TeamCreated, TeamDeleted, TeamUpdated};

class Team extends JetstreamTeam
{
    use HasUuid;
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'personal_team' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'personal_team',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];
}
