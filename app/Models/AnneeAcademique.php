<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnneeAcademique extends Model
{
    use SoftDeletes;

    protected $table = 'annees_academiques';
    protected $primaryKey = 'id';

    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin'   => 'date',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function tauxHoraires(): HasMany
    {
        return $this->hasMany(TauxHoraire::class, 'id_annee', 'id');
    }

    public function affectationsCours(): HasMany
    {
        return $this->hasMany(AffectationCours::class, 'id_annee', 'id');
    }

    public function etatsPaiement(): HasMany
    {
        return $this->hasMany(EtatPaiement::class, 'id_annee', 'id');
    }
}
