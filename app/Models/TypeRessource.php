<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeRessource extends Model
{
    use SoftDeletes;

    protected $table = 'type_ressources';
    protected $primaryKey = 'id';

    protected $fillable = [
        'libelle',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function ressourcesPedagogiques(): HasMany
    {
        return $this->hasMany(RessourcePedagogique::class, 'id_type', 'id');
    }
}
