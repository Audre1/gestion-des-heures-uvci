<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;

class Utilisateur extends Authenticatable implements CanResetPassword
{
    use Notifiable, SoftDeletes;

    /**
     * La table 'users' correspond à l'entité UTILISATEUR du MCD.
     */
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'login',
        'mot_de_passe',
        'date_creation',
        'statut_compte',
        'id_role',
        'created_by',
    ];

    protected $casts = [
        'id_role' => 'integer',
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    /**
     * Mappe le champ 'password' de Laravel Auth sur notre 'mot_de_passe'.
     */
    public function getAuthPassword(): string
    {
        return $this->mot_de_passe;
    }

    protected function casts(): array
    {
        return [
            'date_creation' => 'datetime',
            'mot_de_passe'  => 'hashed',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    /**
     * Un utilisateur appartient à un rôle.
     */
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id');
    }

    /**
     * Un utilisateur est lié à un enseignant.
     */
    public function enseignant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Enseignant::class, 'id_utilisateur', 'id');
    }

    /**
     * Un utilisateur peut être créé par un autre utilisateur.
     */
    public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'created_by', 'id');
    }

    /**
     * Envoie la notification de réinitialisation de mot de passe.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
