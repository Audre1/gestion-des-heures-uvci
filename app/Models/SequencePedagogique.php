<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SequencePedagogique extends Model
{
    use SoftDeletes;

    protected $table = 'sequences_pedagogiques';
    protected $primaryKey = 'id';

    protected $fillable = [
        'titre',
        'numero_ordre',
        'id_cours',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function cours(): BelongsTo
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id');
    }

    public function ressourcesPedagogiques(): HasMany
    {
        return $this->hasMany(RessourcePedagogique::class, 'id_sequence', 'id');
    }
}
