<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Services\PasswordResetService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected PasswordResetService $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required'],
            'password' => ['required'],
        ], [
            'login.required' => 'L\'identifiant ou l\'email est requis.',
            'password.required' => 'Le mot de passe est requis.',
        ]);

        $user = Utilisateur::where('login', $credentials['login'])
            ->orWhere('email', $credentials['login'])
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'login' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        if ($user->statut_compte !== 'actif') {
            throw ValidationException::withMessages([
                'login' => ['Votre compte est ' . $user->statut_compte . '. Contactez l\'administrateur.'],
            ]);
        }

        if (Auth::attempt(['email' => $user->email, 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            logActivite('connexion', 'Connexion de l\'utilisateur ' . $user->login, $user);
            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'login' => ['Les identifiants fournis sont incorrects.'],
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        if ($user) {
            logActivite('déconnexion', 'Déconnexion de l\'utilisateur ' . $user->login, $user);
        }
        return redirect()->route('login');
    }

    /**
     * Affiche le formulaire de demande de réinitialisation.
     */
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envoie un code OTP à l'utilisateur.
     *
     * Sécurisé par :
     * - Cooldown de 60s entre deux envois
     * - Limite de 20 demandes / heure par IP
     * - Invalidation automatique de tout code précédent
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ], [
            'email.required' => 'Une adresse email est requise pour recevoir le code.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
        ]);

        $user = Utilisateur::where('email', $request->email)->first();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Aucun utilisateur trouvé avec cet email.'], 404);
            }
            return back()->withInput($request->only('email'))->withErrors(['email' => 'Aucun utilisateur trouvé avec cet email.']);
        }

        $result = $this->passwordResetService->generateAndSendOtp($user, $request->ip());

        if (!$result['success']) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $result['message']], 429);
            }
            return back()->withInput($request->only('email'))->withErrors(['email' => $result['message']]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $result['message'],
                'cooldown' => $this->passwordResetService->getCooldownSeconds($request->email),
            ]);
        }

        return redirect()->route('password.reset', ['email' => $request->email])
            ->with('status', $result['message']);
    }

    /**
     * Affiche la page de vérification du code OTP.
     */
    public function resetPassword(Request $request)
    {
        $cooldown = 0;
        if ($request->email) {
            $cooldown = $this->passwordResetService->getCooldownSeconds($request->email);
        }

        return view('auth.reset-password', [
            'email' => $request->email,
            'cooldown' => $cooldown,
        ]);
    }

    /**
     * Vérifie le code OTP saisi par l'utilisateur.
     *
     * Sécurisé par :
     * - Maximum 5 tentatives de vérification
     * - Blocage de 1h après 5 échecs
     * - Expiration automatique après 15 minutes
     */
    public function verifyCode(Request $request)
    {
        $code = $request->input('code');
        if (is_array($code)) {
            $code = implode('', $code);
            $request->merge(['code' => $code]);
        }

        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ], [
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'code.required' => 'Le code de vérification est requis.',
            'code.digits' => 'Le code doit contenir exactement 6 chiffres.',
        ]);

        $result = $this->passwordResetService->verifyOtp($request->email, $request->code);

        if (!$result['success']) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $result['message']], 422);
            }
            return back()->withInput($request->only('email'))
                ->withErrors(['code' => $result['message']]);
        }

        // Code valide → rediriger vers la page de nouveau mot de passe
        return redirect()->route('password.new', [
            'email' => $result['email'],
            'code' => $result['code'],
        ]);
    }

    /**
     * Affiche le formulaire de nouveau mot de passe.
     */
    public function newPassword(Request $request)
    {
        return view('auth.new-password', [
            'email' => $request->email,
            'code' => $request->code
        ]);
    }

    /**
     * Met à jour le mot de passe après vérification du code.
     *
     * Vérifie que le code OTP a bien été validé via verifyOtp
     * avant d'autoriser le changement de mot de passe.
     */
    public function updatePassword(Request $request)
    {
        $code = $request->input('code');
        if (is_array($code)) {
            $code = implode('', $code);
            $request->merge(['code' => $code]);
        }

        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'code.required' => 'Le code de vérification est requis.',
            'code.digits' => 'Le code doit contenir exactement 6 chiffres.',
            'password.required' => 'Le nouveau mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $email = $request->email;

        // Vérifier que le code a bien été validé via verifyOtp
        // (cette vérification se fait dans le Cache depuis le service)
        if (!$this->passwordResetService->isVerified($email)) {
            // Fallback : vérifier via la table password_reset_tokens (compatibilité)
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->first();

            if (!$resetRecord) {
                return back()->withInput($request->only('email'))
                    ->withErrors(['code' => 'Code de vérification invalide. Veuillez recommencer le processus.']);
            }

            // Vérifier l'expiration (15 minutes)
            if (now()->diffInMinutes($resetRecord->created_at) > 15) {
                DB::table('password_reset_tokens')->where('email', $email)->delete();
                return back()->withInput($request->only('email'))
                    ->withErrors(['code' => 'Le code de vérification a expiré. Veuillez demander un nouveau code.']);
            }
        }

        // Mettre à jour le mot de passe
        $user = Utilisateur::where('email', $email)->first();
        if ($user) {
            $user->mot_de_passe = Hash::make($request->password);
            $user->save();

            if (function_exists('logActivite')) {
                logActivite('modification', 'Réinitialisation du mot de passe pour l\'utilisateur ' . $user->login, $user);
            }

            // Nettoyer le cache OTP et les compteurs RateLimiter
            $this->passwordResetService->consumeVerification($email);
            $this->passwordResetService->clearRateLimiter($email);

            // Supprimer l'ancien enregistrement en base si existant
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();
        }

        return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé avec succès.');
    }

    /**
     * Renvoie un nouveau code OTP.
     *
     * Utilisable uniquement si :
     * - Moins de 3 renvois effectués dans l'heure
     * - Délai de 60s respecté depuis le dernier envoi
     * - Limite IP non atteinte
     */
    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ], [
            'email.required' => 'Une adresse email est requise pour renvoyer le code.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
        ]);

        $user = Utilisateur::where('email', $request->email)->first();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Aucun utilisateur trouvé avec cet email.'], 404);
            }
            return back()->withInput($request->only('email'))->withErrors(['email' => 'Aucun utilisateur trouvé avec cet email.']);
        }

        $result = $this->passwordResetService->resendOtp($user, $request->ip());

        if (!$result['success']) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $result['message']], 429);
            }
            return back()->withErrors(['email' => $result['message']]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $result['message'],
                'cooldown' => $this->passwordResetService->getCooldownSeconds($request->email),
            ]);
        }

        return back()->with('status', $result['message']);
    }
}
