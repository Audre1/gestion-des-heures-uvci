<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivitePedagogique extends Model
{
    use SoftDeletes;

    protected $table = 'activites_pedagogiques';
    protected $primaryKey = 'id';

    protected $fillable = [
        'type_activite',
        'date_activite',
        'statut',
        'coefficient',
        'nb_sequences',
        'volume_horaire',
        'id_affectation',
        'id_ressource',
        'id_niveau',
    ];

    protected function casts(): array
    {
        return [
            'date_activite'  => 'date',
            'coefficient'    => 'decimal:2',
            'volume_horaire' => 'decimal:2',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function affectationCours(): BelongsTo
    {
        return $this->belongsTo(AffectationCours::class, 'id_affectation', 'id');
    }

    public function ressourcePedagogique(): BelongsTo
    {
        return $this->belongsTo(RessourcePedagogique::class, 'id_ressource', 'id');
    }

    public function niveauComplexite(): BelongsTo
    {
        return $this->belongsTo(NiveauComplexite::class, 'id_niveau', 'id');
    }
}
