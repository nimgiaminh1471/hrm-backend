<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Success Response
     */
    public function sendResponse($result, $message, $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * Error Response
     */
    public function sendError($error, $errorMessages = [], $code = 404): JsonResponse
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
     * Validation Error Response
     */
    public function sendValidationError($errors, $code = 422): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => 'Validation Error',
            'errors' => $errors,
        ];

        return response()->json($response, $code);
    }

    /**
     * Unauthorized Response
     */
    public function sendUnauthorized($message = 'Unauthorized', $code = 401): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * Forbidden Response
     */
    public function sendForbidden($message = 'Forbidden', $code = 403): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }
} 