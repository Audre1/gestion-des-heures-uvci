<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NiveauComplexite extends Model
{
    use SoftDeletes;

    protected $table = 'niveaux_complexite';
    protected $primaryKey = 'id';

    protected $fillable = [
        'libelle',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function activitesPedagogiques(): HasMany
    {
        return $this->hasMany(ActivitePedagogique::class, 'id_niveau', 'id');
    }
}
