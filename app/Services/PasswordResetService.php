<?php

namespace App\Services;

use App\Mail\ResetPasswordMail;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Service de réinitialisation sécurisée du mot de passe.
 *
 * Sécurise le processus OTP avec :
 * - RateLimiter pour le cooldown, les renvois, les tentatives de vérification et les IP
 * - Cache pour l'expiration automatique du code à 15 minutes
 * - Un seul code valide à la fois par email
 */
class PasswordResetService
{
    /**
     * Durée de validité du code OTP (en minutes).
     */
    const OTP_TTL_MINUTES = 15;

    /**
     * Intervalle minimum entre deux envois de code (en secondes).
     */
    const COOLDOWN_SECONDS = 60;

    /**
     * Nombre maximum de renvois autorisés par période.
     */
    const MAX_RESENDS = 3;

    /**
     * Période pour la limite de renvois (en minutes).
     */
    const RESEND_WINDOW_MINUTES = 60;

    /**
     * Nombre maximum de tentatives de vérification.
     */
    const MAX_VERIFY_ATTEMPTS = 5;

    /**
     * Durée de blocage après épuisement des tentatives (en minutes).
     */
    const VERIFY_BLOCK_MINUTES = 60;

    /**
     * Nombre maximum de demandes par adresse IP par heure.
     */
    const MAX_REQUESTS_PER_IP = 20;

    /**
     * Durée de la fenêtre de limitation par IP (en minutes).
     */
    const IP_WINDOW_MINUTES = 60;

    // ─── Clés pour le Cache / RateLimiter ─────────────────────────────────

    private function otpCacheKey(string $email): string
    {
        return 'password_reset_otp:' . strtolower($email);
    }

    private function cooldownKey(string $email): string
    {
        return 'send-reset-cooldown:' . strtolower($email);
    }

    private function resendKey(string $email): string
    {
        return 'resend-reset:' . strtolower($email);
    }

    private function verifyKey(string $email): string
    {
        return 'verify-otp:' . strtolower($email);
    }

    private function ipKey(string $ip): string
    {
        return 'reset-by-ip:' . $ip;
    }

    private function verifiedKey(string $email): string
    {
        return 'password_reset_verified:' . strtolower($email);
    }

    // ─── Vérifications de limites ─────────────────────────────────────────

    /**
     * Vérifie si l'utilisateur peut demander un nouveau code.
     *
     * @return array{allowed: bool, message: ?string, remaining: ?int}
     */
    public function canRequestCode(string $email): array
    {
        $key = $this->cooldownKey($email);

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return [
                'allowed' => false,
                'message' => "Veuillez attendre encore {$seconds} seconde(s) avant de renvoyer un code.",
                'remaining' => $seconds,
            ];
        }

        return ['allowed' => true, 'message' => null, 'remaining' => 0];
    }

    /**
     * Vérifie si l'utilisateur peut renvoyer un code (limite de 3/heure).
     *
     * @return array{allowed: bool, message: ?string}
     */
    public function canResendCode(string $email): array
    {
        $key = $this->resendKey($email);

        if (RateLimiter::tooManyAttempts($key, self::MAX_RESENDS)) {
            return [
                'allowed' => false,
                'message' => 'Vous avez effectué trop de demandes. Veuillez réessayer dans une heure.',
            ];
        }

        return ['allowed' => true, 'message' => null];
    }

    /**
     * Vérifie si l'utilisateur peut tenter de vérifier un code.
     *
     * @return array{allowed: bool, message: ?string, remaining: ?int}
     */
    public function canVerifyCode(string $email): array
    {
        $key = $this->verifyKey($email);

        if (RateLimiter::tooManyAttempts($key, self::MAX_VERIFY_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);
            return [
                'allowed' => false,
                'message' => "Trop de tentatives échouées. Veuillez réessayer dans " . ceil($seconds / 60) . " minute(s).",
                'remaining' => $seconds,
            ];
        }

        return ['allowed' => true, 'message' => null, 'remaining' => 0];
    }

    /**
     * Vérifie la limite par adresse IP.
     *
     * @return array{allowed: bool, message: ?string}
     */
    public function canRequestFromIp(string $ip): array
    {
        $key = $this->ipKey($ip);

        if (RateLimiter::tooManyAttempts($key, self::MAX_REQUESTS_PER_IP)) {
            return [
                'allowed' => false,
                'message' => 'Trop de demandes depuis cette adresse IP. Veuillez réessayer plus tard.',
            ];
        }

        return ['allowed' => true, 'message' => null];
    }

    // ─── Actions métier ───────────────────────────────────────────────────

    /**
     * Génère et envoie un code OTP à l'utilisateur.
     * Invalide automatiquement tout code précédent pour cet email.
     *
     * @return array{success: bool, message: string}
     */
    public function generateAndSendOtp(Utilisateur $user, string $ip): array
    {
        $email = $user->email;

        // Vérifier la limite par IP
        $ipCheck = $this->canRequestFromIp($ip);
        if (!$ipCheck['allowed']) {
            return ['success' => false, 'message' => $ipCheck['message']];
        }

        // Vérifier le cooldown
        $cooldownCheck = $this->canRequestCode($email);
        if (!$cooldownCheck['allowed']) {
            return ['success' => false, 'message' => $cooldownCheck['message']];
        }

        // Générer un code OTP à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Stocker dans le cache (écrase l'ancien automatiquement → invalidation)
        Cache::put($this->otpCacheKey($email), $code, now()->addMinutes(self::OTP_TTL_MINUTES));

        // Enregistrer la tentative dans le RateLimiter (cooldown)
        RateLimiter::hit($this->cooldownKey($email), self::COOLDOWN_SECONDS);

        // Enregistrer la tentative dans le RateLimiter IP
        RateLimiter::hit($this->ipKey($ip), self::IP_WINDOW_MINUTES * 60);

        // Envoyer l'email
        $nomComplet = trim(($user->prenom ?? '') . ' ' . ($user->nom ?? ''));
        Mail::to($user->email)->send(new ResetPasswordMail($code, $nomComplet));

        return ['success' => true, 'message' => 'Un code de vérification a été envoyé à votre adresse email.'];
    }

    /**
     * Renvoie un nouveau code OTP (vérifie la limite de renvois + cooldown).
     *
     * @return array{success: bool, message: string}
     */
    public function resendOtp(Utilisateur $user, string $ip): array
    {
        $email = $user->email;

        // Vérifier la limite par IP
        $ipCheck = $this->canRequestFromIp($ip);
        if (!$ipCheck['allowed']) {
            return ['success' => false, 'message' => $ipCheck['message']];
        }

        // Vérifier la limite de renvois (3/heure)
        $resendCheck = $this->canResendCode($email);
        if (!$resendCheck['allowed']) {
            return ['success' => false, 'message' => $resendCheck['message']];
        }

        // Vérifier le cooldown
        $cooldownCheck = $this->canRequestCode($email);
        if (!$cooldownCheck['allowed']) {
            return ['success' => false, 'message' => $cooldownCheck['message']];
        }

        // Générer un nouveau code (invalide l'ancien)
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put($this->otpCacheKey($email), $code, now()->addMinutes(self::OTP_TTL_MINUTES));

        // Incrémenter les compteurs
        RateLimiter::hit($this->cooldownKey($email), self::COOLDOWN_SECONDS);
        RateLimiter::hit($this->resendKey($email), self::RESEND_WINDOW_MINUTES * 60);
        RateLimiter::hit($this->ipKey($ip), self::IP_WINDOW_MINUTES * 60);

        // Envoyer l'email
        $nomComplet = trim(($user->prenom ?? '') . ' ' . ($user->nom ?? ''));
        Mail::to($user->email)->send(new ResetPasswordMail($code, $nomComplet));

        return ['success' => true, 'message' => 'Un nouveau code de vérification a été envoyé à votre adresse email.'];
    }

    /**
     * Vérifie le code OTP saisi.
     *
     * @return array{success: bool, message: string, email?: string, code?: string}
     */
    public function verifyOtp(string $email, string $code): array
    {
        // Vérifier la limite de tentatives
        $verifyCheck = $this->canVerifyCode($email);
        if (!$verifyCheck['allowed']) {
            return ['success' => false, 'message' => $verifyCheck['message']];
        }

        // Récupérer le code stocké
        $storedCode = Cache::get($this->otpCacheKey($email));

        // Code inexistant (expiré ou jamais généré)
        if ($storedCode === null) {
            RateLimiter::hit($this->verifyKey($email), self::VERIFY_BLOCK_MINUTES * 60);
            return ['success' => false, 'message' => 'Le code de vérification a expiré ou est invalide. Veuillez demander un nouveau code.'];
        }

        // Code incorrect
        if (!hash_equals((string) $storedCode, (string) $code)) {
            $attemptsLeft = max(0, self::MAX_VERIFY_ATTEMPTS - RateLimiter::attempts($this->verifyKey($email)) - 1);
            RateLimiter::hit($this->verifyKey($email), self::VERIFY_BLOCK_MINUTES * 60);

            if ($attemptsLeft > 0) {
                return ['success' => false, 'message' => "Code incorrect. Il vous reste {$attemptsLeft} tentative(s)."];
            }

            return ['success' => false, 'message' => 'Trop de tentatives échouées. Veuillez réessayer dans 60 minutes.'];
        }

        // Code valide
        // Marquer la vérification comme réussie dans le cache (durée courte : 10 min)
        // Le code OTP reste dans le cache pour la vérification finale dans updatePassword
        Cache::put($this->verifiedKey($email), true, now()->addMinutes(10));

        return [
            'success' => true,
            'message' => 'Code vérifié avec succès.',
            'email' => $email,
            'code' => $code,
        ];
    }

    /**
     * Vérifie si le code OTP a été validé avec succès pour cet email.
     */
    public function isVerified(string $email): bool
    {
        return Cache::get($this->verifiedKey($email), false) === true;
    }

    /**
     * Consomme la vérification (appelé après le changement de mot de passe réussi).
     */
    public function consumeVerification(string $email): void
    {
        Cache::forget($this->verifiedKey($email));
        Cache::forget($this->otpCacheKey($email));
    }

    /**
     * Retourne le temps restant avant de pouvoir renvoyer un code (en secondes).
     */
    public function getCooldownSeconds(string $email): int
    {
        $key = $this->cooldownKey($email);
        return RateLimiter::availableIn($key);
    }

    /**
     * Nettoie les compteurs RateLimiter pour un email donné.
     */
    public function clearRateLimiter(string $email): void
    {
        RateLimiter::clear($this->cooldownKey($email));
        RateLimiter::clear($this->resendKey($email));
        RateLimiter::clear($this->verifyKey($email));
    }
}