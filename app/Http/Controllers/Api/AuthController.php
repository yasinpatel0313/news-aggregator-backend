<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function getToken(): JsonResponse
    {
        try {
            $payload = [
                'client_id' => 'news_app',
                'issued_at' => time(),
                'expires_at' => time() + (60 * 60 * 24), // 24 hours
            ];

            $token = base64_encode(json_encode($payload));

            return response()->json([
                'success' => true,
                'message' => 'Token generated successfully',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => 60 * 60 * 24,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
