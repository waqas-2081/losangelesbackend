<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogoCreator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LogoCreatorController extends Controller
{
    /**
     * POST /api/logo-creator/autosave
     *
     * Called every time the user pauses typing (debounced ~3-4s on the frontend).
     * On the very first call there is no session_token yet, so we create a new
     * draft row and hand the token back — the frontend must store it and send
     * it with every subsequent autosave call so we update the same row.
     */
    public function autosave(Request $request)
    {
        $validated = $request->validate([
            'session_token' => 'nullable|string|max:64',
            'business_name' => 'nullable|string|max:255',
            'slogan'        => 'nullable|string|max:255',
            'industry'      => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:30',
            'current_step'  => 'nullable|integer|min:1|max:3',
        ]);

        $sessionToken = $validated['session_token'] ?? (string) Str::uuid();

        $brief = LogoCreator::firstOrNew(['session_token' => $sessionToken]);

        // Only update fields that were actually sent so partial drafts don't wipe data.
        foreach (['business_name', 'slogan', 'industry', 'email', 'phone', 'current_step'] as $field) {
            if (array_key_exists($field, $validated)) {
                $brief->{$field} = $validated[$field];
            }
        }

        $brief->session_token = $sessionToken;

        // Mark as complete once we're on the final step and have an email
        $finalStep = $validated['current_step'] ?? $brief->current_step ?? 1;
        if (!empty($brief->email) && (int) $finalStep >= 3) {
            $brief->is_complete = true;
        }

        $brief->save();

        return response()->json([
            'success'       => true,
            'session_token' => $sessionToken,
            'id'            => $brief->id,
            'is_complete'   => (bool) $brief->is_complete,
        ]);
    }
}