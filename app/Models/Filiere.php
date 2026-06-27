<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filiere extends Model
{
    use SoftDeletes;

    protected $table = 'filieres';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nom_filiere',
        'id_departement',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class, 'id_departement', 'id');
    }

    public function cours(): BelongsToMany
    {
        return $this->belongsToMany(
            Cours::class,
            'filiere_cours',
            'id_filiere',
            'id_cours'
        )->withTimestamps();
    }
}
