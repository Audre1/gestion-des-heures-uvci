<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TauxHoraire extends Model
{
    use SoftDeletes;

    protected $table = 'taux_horaires';
    protected $primaryKey = 'id';

    protected $fillable = [
        'montant',
        'devise',
        'date_application',
        'date_fin_application',
        'id_grade',
        'id_annee',
    ];

    protected function casts(): array
    {
        return [
            'montant'              => 'decimal:2',
            'date_application'     => 'date',
            'date_fin_application' => 'date',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'id_grade', 'id');
    }

    public function anneeAcademique(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'id_annee', 'id');
    }
}
