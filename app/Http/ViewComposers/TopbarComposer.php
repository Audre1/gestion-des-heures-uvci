<?php

namespace App\Http\ViewComposers;

use App\Models\AnneeAcademique;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TopbarComposer
{
    /**
     * Partage les données nécessaires à la vue 'partials.topbar'.
     */
    public function compose(View $view): void
    {
        $utilisateur = Auth::user();

        $currentYear = AnneeAcademique::where('statut', 'en_cours')->first();
        $userRole    = $utilisateur?->role?->code;

        // Placeholder de recherche adapté au rôle
        $searchPlaceholder = match ($userRole) {
            'admin'      => 'Rechercher un enseignant, un cours, une filière...',
            'secretaire' => 'Rechercher un enseignant, un cours, une filière...',
            'enseignant' => 'Rechercher dans mes activités, mes cours...',
            default      => 'Rechercher...',
        };

        // Nom d'utilisateur formaté
        $userName = $utilisateur
            ? trim(($utilisateur->prenom ?? '') . ' ' . ($utilisateur->nom ?? ''))
            : 'Utilisateur';

        // Libellé du rôle
        $roleLabel = $utilisateur?->role?->libelle ?? 'Utilisateur';

        // Badge de statut d'année
        $yearStatusBadge = function (?string $statut): ?array {
            return match ($statut) {
                'en_cours'  => ['label' => 'Actif',    'class' => 'badge-soft-green'],
                'cloturee'  => ['label' => 'Clôturée', 'class' => 'badge-soft-gray'],
                'planifiee' => ['label' => 'Planifiée','class' => 'badge-soft-blue'],
                default     => null,
            };
        };

        $view->with(compact(
            'currentYear',
            'userRole',
            'searchPlaceholder',
            'userName',
            'roleLabel',
            'yearStatusBadge',
        ));
    }
}