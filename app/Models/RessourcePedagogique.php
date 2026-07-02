<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RessourcePedagogique extends Model
{
    use SoftDeletes;

    protected $table = 'ressources_pedagogiques';
    protected $primaryKey = 'id';

    protected $fillable = [
        'titre',
        'date_creation',
        'date_modification',
        'id_sequence',
        'id_type',
    ];

    protected function casts(): array
    {
        return [
            'date_creation'     => 'datetime',
            'date_modification' => 'datetime',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(SequencePedagogique::class, 'id_sequence', 'id');
    }

    public function typeRessource(): BelongsTo
    {
        return $this->belongsTo(TypeRessource::class, 'id_type', 'id');
    }

    public function activitesPedagogiques(): HasMany
    {
        return $this->hasMany(ActivitePedagogique::class, 'id_ressource', 'id');
    }
}
