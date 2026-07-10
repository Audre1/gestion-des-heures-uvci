<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function authenticate(Request $request)
{


    $credentials = $request->validate([
        'login' => ['required'],
        'password' => ['required'],
    ]);

    if (Auth::attempt([
        'email' => $credentials['login'],
        'password' => $credentials['password']
    ])) {

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    return back()->withErrors([
        'login' => 'Identifiant ou mot de passe incorrect.',
    ]);
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
            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'login' => ['Les identifiants fournis sont incorrects.'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

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
            return back()->withInput($request->only('email'))->withErrors(['email' => 'Aucun utilisateur trouvé avec cet email.']);
        }

        // Générer un code OTP à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Stocker le code dans la table password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $code,
                'created_at' => now()
            ]
        );

        // Envoyer l'email avec le code
        Mail::to($user->email)->send(new \App\Mail\ResetPasswordMail($code, $user->prenom . ' ' . $user->nom));

        return redirect()->route('password.reset', ['email' => $request->email])->with('status', 'Un code de vérification a été envoyé à votre adresse email.');
    }

    public function resetPassword(Request $request)
    {
        return view('auth.reset-password', ['email' => $request->email]);
    }

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
            'email.required' => 'L’adresse email est requise.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'code.required' => 'Le code de vérification est requis.',
            'code.digits' => 'Le code doit contenir exactement 6 chiffres.',
        ]);

        // Vérifier le code OTP
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$resetRecord) {
            return back()->withInput($request->only('email', 'code'))
                ->withErrors(['code' => 'Le code de vérification est invalide.']);
        }

        // Vérifier l'expiration (15 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 15) {
            return back()->withInput($request->only('email', 'code'))
                ->withErrors(['code' => 'Le code de vérification a expiré. Veuillez demander un nouveau code.']);
        }

        // Code valide, rediriger vers la page de modification du mot de passe
        return redirect()->route('password.new', ['email' => $request->email, 'code' => $request->code]);
    }

    public function newPassword(Request $request)
    {
        return view('auth.new-password', [
            'email' => $request->email,
            'code' => $request->code
        ]);
    }

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
            'email.required' => 'L’adresse email est requise.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'code.required' => 'Le code de vérification est requis.',
            'code.digits' => 'Le code doit contenir exactement 6 chiffres.',
            'password.required' => 'Le nouveau mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        // Vérifier le code OTP
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$resetRecord) {
            return back()->withInput($request->only('email'))
                ->withErrors(['code' => 'Le code de vérification est invalide.']);
        }

        // Vérifier l'expiration (15 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 15) {
            return back()->withInput($request->only('email'))
                ->withErrors(['code' => 'Le code de vérification a expiré. Veuillez demander un nouveau code.']);
        }

        // Mettre à jour le mot de passe
        $user = Utilisateur::where('email', $request->email)->first();
        if ($user) {
            $user->mot_de_passe = Hash::make($request->password);
            $user->save();

            // Supprimer le token utilisé
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();
        }

        return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé avec succès.');
    }

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
            return back()->withInput($request->only('email'))->withErrors(['email' => 'Aucun utilisateur trouvé avec cet email.']);
        }

        // Générer un nouveau code OTP à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Mettre à jour le code dans la table password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $code,
                'created_at' => now()
            ]
        );

        // Envoyer l'email avec le nouveau code
        Mail::to($user->email)->send(new \App\Mail\ResetPasswordMail($code, $user->prenom . ' ' . $user->nom));

        return back()->with('status', 'Un nouveau code de vérification a été envoyé à votre adresse email.');
    }
}
