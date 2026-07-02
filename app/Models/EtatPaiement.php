<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtatPaiement extends Model
{
    use SoftDeletes;

    protected $table = 'etats_paiement';
    protected $primaryKey = 'id';

    protected $fillable = [
        'date_generation',
        'periode',
        'montant_total',
        'statut',
        'format_export',
        'id_enseignant',
        'id_annee',
    ];

    protected function casts(): array
    {
        return [
            'date_generation' => 'datetime',
            'montant_total'   => 'decimal:2',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(Enseignant::class, 'id_enseignant', 'id');
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'id_annee', 'id');
    }
}
