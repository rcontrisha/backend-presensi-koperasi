<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function updatePassword(Request $request): JsonResponse
    {
        // Validasi input
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);
    
        // Update password user
        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);
    
        // Kembalikan response dalam format JSON
        return response()->json([
            'message' => 'Password berhasil diperbarui',
        ], 200);
    }
}
