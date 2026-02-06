<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminMfaCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($validated, $request->boolean('remember'))) {
            return response()->json(['message' => 'Login fehlgeschlagen.'], 422);
        }

        $user = $request->user();
        if (!$user->is_admin) {
            Auth::logout();
            return response()->json(['message' => 'Kein Admin-Zugriff.'], 403);
        }

        if (!$user->mfa_totp_enabled && !$user->mfa_email_enabled) {
            $user->forceFill(['mfa_email_enabled' => true])->save();
        }

        if ($user->mfa_totp_enabled || $user->mfa_email_enabled) {
            $request->session()->put('admin_mfa_user', $user->id);
            Auth::logout();
            return response()->json([
                'mfa_required' => true,
                'methods' => [
                    'totp' => $user->mfa_totp_enabled,
                    'email' => !$user->mfa_totp_enabled && $user->mfa_email_enabled,
                ],
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['status' => 'ok']);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['authenticated' => false]);
        }

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'mfa_totp_enabled' => $user->mfa_totp_enabled,
                'mfa_email_enabled' => $user->mfa_email_enabled,
            ],
        ]);
    }

    public function csrf(Request $request)
    {
        $request->session()->regenerateToken();

        return response()->json([
            'token' => csrf_token(),
        ]);
    }

    public function mfaTotpSetup(Request $request)
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        $request->session()->put('admin_mfa_totp_secret', $secret);

        $user = $request->user();
        $url = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'secret' => $secret,
            'otpauth_url' => $url,
        ]);
    }

    public function mfaTotpVerify(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $secret = $request->session()->get('admin_mfa_totp_secret');
        if (!$secret) {
            return response()->json(['message' => 'Kein Setup gestartet.'], 422);
        }

        $google2fa = new Google2FA();
        if (!$google2fa->verifyKey($secret, $validated['code'])) {
            return response()->json(['message' => 'Ungültiger Code.'], 422);
        }

        $user = $request->user();
        $user->forceFill([
            'mfa_totp_enabled' => true,
            'mfa_totp_secret' => $secret,
        ])->save();

        $request->session()->forget('admin_mfa_totp_secret');

        return response()->json(['status' => 'enabled']);
    }

    public function mfaTotpDisable(Request $request)
    {
        $user = $request->user();
        $user->forceFill([
            'mfa_totp_enabled' => false,
            'mfa_totp_secret' => null,
        ])->save();

        return response()->json(['status' => 'disabled']);
    }

    public function mfaEmailSend(Request $request)
    {
        $user = $request->user();
        if (!$user->mfa_email_enabled && $user->mfa_totp_enabled) {
            return response()->json(['message' => 'E-Mail MFA nicht aktiviert.'], 422);
        }

        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            'mfa_email_code_hash' => Hash::make($code),
            'mfa_email_expires_at' => now()->addMinutes(10),
        ])->save();

        Mail::to($user->email)->send(new AdminMfaCode($code));

        return response()->json(['status' => 'sent']);
    }

    public function mfaEmailVerify(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user();
        if (!$user->mfa_email_code_hash || !$user->mfa_email_expires_at) {
            return response()->json(['message' => 'Kein Code vorhanden.'], 422);
        }

        if (now()->greaterThan($user->mfa_email_expires_at)) {
            return response()->json(['message' => 'Code abgelaufen.'], 422);
        }

        if (!Hash::check($validated['code'], $user->mfa_email_code_hash)) {
            return response()->json(['message' => 'Ungültiger Code.'], 422);
        }

        $user->forceFill([
            'mfa_email_enabled' => true,
            'mfa_email_code_hash' => null,
            'mfa_email_expires_at' => null,
        ])->save();

        return response()->json(['status' => 'enabled']);
    }

    public function mfaEmailDisable(Request $request)
    {
        $user = $request->user();
        if (!$user->mfa_totp_enabled) {
            return response()->json(['message' => 'E-Mail MFA ist Pflicht solange TOTP deaktiviert ist.'], 422);
        }
        $user->forceFill([
            'mfa_email_enabled' => false,
            'mfa_email_code_hash' => null,
            'mfa_email_expires_at' => null,
        ])->save();

        return response()->json(['status' => 'disabled']);
    }

    public function mfaLoginTotp(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $userId = $request->session()->get('admin_mfa_user');
        if (!$userId) {
            return response()->json(['message' => 'Kein MFA-Login aktiv.'], 422);
        }

        $user = User::findOrFail($userId);
        if (!$user->is_admin) {
            return response()->json(['message' => 'Kein Admin-Zugriff.'], 403);
        }
        $google2fa = new Google2FA();

        if (!$user->mfa_totp_enabled || !$user->mfa_totp_secret) {
            return response()->json(['message' => 'TOTP nicht aktiviert.'], 422);
        }

        if (!$google2fa->verifyKey($user->mfa_totp_secret, $validated['code'])) {
            return response()->json(['message' => 'Ungültiger Code.'], 422);
        }

        Auth::login($user);
        $request->session()->forget('admin_mfa_user');

        return response()->json(['status' => 'ok']);
    }

    public function mfaLoginEmailSend(Request $request)
    {
        $userId = $request->session()->get('admin_mfa_user');
        if (!$userId) {
            return response()->json(['message' => 'Kein MFA-Login aktiv.'], 422);
        }

        $user = User::findOrFail($userId);
        if (!$user->is_admin) {
            return response()->json(['message' => 'Kein Admin-Zugriff.'], 403);
        }
        if (!$user->mfa_email_enabled && $user->mfa_totp_enabled) {
            return response()->json(['message' => 'E-Mail MFA nicht aktiviert.'], 422);
        }

        $code = (string) random_int(100000, 999999);
        $user->forceFill([
            'mfa_email_code_hash' => Hash::make($code),
            'mfa_email_expires_at' => now()->addMinutes(10),
        ])->save();

        Mail::to($user->email)->send(new AdminMfaCode($code));

        return response()->json(['status' => 'sent']);
    }

    public function mfaLoginEmailVerify(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $userId = $request->session()->get('admin_mfa_user');
        if (!$userId) {
            return response()->json(['message' => 'Kein MFA-Login aktiv.'], 422);
        }

        $user = User::findOrFail($userId);
        if (!$user->is_admin) {
            return response()->json(['message' => 'Kein Admin-Zugriff.'], 403);
        }
        if (!$user->mfa_email_code_hash || !$user->mfa_email_expires_at) {
            return response()->json(['message' => 'Kein Code vorhanden.'], 422);
        }

        if (now()->greaterThan($user->mfa_email_expires_at)) {
            return response()->json(['message' => 'Code abgelaufen.'], 422);
        }

        if (!Hash::check($validated['code'], $user->mfa_email_code_hash)) {
            return response()->json(['message' => 'Ungültiger Code.'], 422);
        }

        Auth::login($user);
        $request->session()->forget('admin_mfa_user');

        return response()->json(['status' => 'ok']);
    }
}
