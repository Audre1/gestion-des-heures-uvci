<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Enseignant;

#[Fillable([
    'email',
    'login',
    'mot_de_passe',
    'statut_compte',
    'id_role'
])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{


public function role()
{
    return $this->belongsTo(Role::class, 'id_role');
}
    
public function getAuthPassword()
{
    return $this->mot_de_passe;
}

/** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
    ];
}

   public function enseignant()
{
    return $this->hasOne(Enseignant::class, 'id_utilisateur', 'id');
}
}
