<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GmailApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private $gmailApiService;

    public function __construct()
    {
        try {
            $this->gmailApiService = new GmailApiService();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Gmail API service', ['error' => $e->getMessage()]);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // LOGIN
    // POST /api/login
    // ══════════════════════════════════════════════════════════════════

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        $token = $user->createToken('dashboard-login')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user'  => [
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'phone'   => $user->phone,
                    'company' => $user->company,
                ],
            ],
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // LOGOUT
    // POST /api/logout   (auth:sanctum)
    // ══════════════════════════════════════════════════════════════════

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Logged out.']);
    }

    // ══════════════════════════════════════════════════════════════════
    // FORGOT PASSWORD — send reset link
    // POST /api/forgot-password
    // ══════════════════════════════════════════════════════════════════

    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Always respond success even if email not found — avoids leaking which emails are registered
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'If that email exists, a reset link has been sent.',
            ]);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $this->sendResetPasswordEmail($user, $token);

        return response()->json([
            'success' => true,
            'message' => 'If that email exists, a reset link has been sent.',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // RESET PASSWORD — verify token, set new password
    // POST /api/reset-password
    // ══════════════════════════════════════════════════════════════════

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'token'    => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();

        if (!$record || !Hash::check($validated['token'], $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'This reset link is invalid.',
            ], 400);
        }

        // Link expires after 60 minutes
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            return response()->json([
                'success' => false,
                'message' => 'This reset link has expired. Please request a new one.',
            ], 400);
        }

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        // Revoke old tokens and log the user straight in on the new password
        $user->tokens()->delete();
        $loginToken = $user->createToken('dashboard-login')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
            'data'    => ['token' => $loginToken],
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // EMAIL — Reset password link
    // ══════════════════════════════════════════════════════════════════

    private function sendResetPasswordEmail(User $user, string $token): void
    {
        try {
            if (!$this->gmailApiService) {
                $this->gmailApiService = new GmailApiService();
            }

            $frontendUrl = rtrim(env('FRONTEND_URL', config('app.url')), '/');
            $resetUrl    = $frontendUrl . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);

            $subject = 'Reset your password — ' . env('APP_NAME');

            $emailContent = '
            <tr>
                <td style="padding: 32px 40px 24px 40px;">
                    <p style="font-size: 15px; color: #333; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px 0;">
                        Hi <strong>' . htmlspecialchars($user->name) . '</strong>,<br><br>
                        We received a request to reset your password. Click the button below to choose a new one. This link expires in 60 minutes.
                    </p>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding: 0 40px 36px 40px;">
                    <a href="' . $resetUrl . '"
                       target="_blank"
                       style="display: inline-block; background-color: #f7a800; color: #1a1a2e; font-family: Arial, Helvetica, sans-serif; font-size: 15px; font-weight: bold; text-decoration: none; padding: 14px 36px; border-radius: 6px;">
                        Reset password
                    </a>
                </td>
            </tr>
            <tr>
                <td style="padding: 0 40px 32px 40px;">
                    <p style="font-size: 13px; color: #aaa; font-family: Arial, Helvetica, sans-serif; margin: 0;">
                        If you did not request a password reset, you can safely ignore this email.
                    </p>
                </td>
            </tr>';

            $htmlBody = $this->buildEmailTemplate($subject, $emailContent);

            $this->gmailApiService->sendEmail($user->email, $subject, $htmlBody, env('APP_NAME'));

            Log::info('Password reset email sent', ['user_id' => $user->id, 'email' => $user->email]);
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email', [
                'user_id' => $user->id ?? null,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    private function buildEmailTemplate(string $subject, string $bodyRows): string
    {
        $frontendUrl = rtrim(env('FRONTEND_URL', config('app.url')), '/');

        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($subject) . '</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="620" cellpadding="0" cellspacing="0" border="0" style="max-width: 620px; width: 100%; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <tr>
                        <td align="center" style="background-color: #1a1a2e; padding: 28px 40px;">
                            <img src="' . $frontendUrl . '/assets/la-logo-designs-DOx3q257.png"
                                 alt="' . env('APP_NAME') . '"
                                 style="max-height: 60px; max-width: 220px; display: block;"
                            />
                        </td>
                    </tr>
                    ' . $bodyRows . '
                    <tr>
                        <td align="center" style="background-color: #1a1a2e; padding: 20px 40px;">
                            <p style="margin: 0; font-size: 13px; color: #aaa; font-family: Arial, Helvetica, sans-serif;">
                                &copy; ' . date('Y') . ' ' . env('APP_NAME') . '. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }
}