<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    /**
     * Success response method.
     */
    public function sendResponse($result, $message, $code = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * Error response method.
     */
    public function sendError($error, $errorMessages = [], $code = Response::HTTP_NOT_FOUND): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * Validation error response method.
     */
    public function sendValidationError($errors, $code = Response::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation Error',
            'errors' => $errors,
        ], $code);
    }

    /**
     * Unauthorized response method.
     */
    public function sendUnauthorized($message = 'Unauthorized', $code = Response::HTTP_UNAUTHORIZED): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }

    /**
     * Forbidden response method.
     */
    public function sendForbidden($message = 'Forbidden', $code = Response::HTTP_FORBIDDEN): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
} 