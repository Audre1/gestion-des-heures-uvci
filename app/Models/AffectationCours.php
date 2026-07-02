<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffectationCours extends Model
{
    use SoftDeletes;

    protected $table = 'affectations_cours';
    protected $primaryKey = 'id';

    protected $fillable = [
        'date_affectation',
        'id_enseignant',
        'id_cours',
        'id_annee',
    ];

    protected function casts(): array
    {
        return [
            'date_affectation' => 'date',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(Enseignant::class, 'id_enseignant', 'id');
    }

    public function cours(): BelongsTo
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id');
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'id_annee', 'id');
    }

    public function activitesPedagogiques(): HasMany
    {
        return $this->hasMany(ActivitePedagogique::class, 'id_affectation', 'id');
    }
}
