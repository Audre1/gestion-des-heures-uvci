<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalActivite extends Model
{
    use SoftDeletes;

    protected $table = 'journal_activites';

    protected $fillable = [
        'utilisateur_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(\App\Models\User::class, 'utilisateur_id');
    }

    public function model()
    {
        return $this->morphTo();
    }
}
