// app/Exceptions/ApiExceptionHandler.php
<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;

class ApiExceptionHandler
{
    public static function handle(\Throwable $e, Request $request): ?JsonResponse
    {
        // Only handle actual exceptions for API routes
        if (!$request->is('api/*')) {
            return null; // Let Laravel handle non-API requests
        }

        return match (true) {
            $e instanceof ValidationException => self::validationError($e),
            $e instanceof NotFoundHttpException => self::notFound($request),
            $e instanceof MethodNotAllowedHttpException => self::methodNotAllowed($request),
            default => self::serverError($e)
        };
    }

    private static function validationError(ValidationException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    }

    private static function notFound(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Endpoint not found'
        ], 404);
    }

    private static function methodNotAllowed(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not allowed'
        ], 405);
    }

    private static function serverError(\Throwable $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Internal server error',
            'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong'
        ], 500);
    }
}
