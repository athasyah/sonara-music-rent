<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class Response
{
    public static function Ok(string $message, mixed $data): JsonResponse
    {
        return response()->json([
            "success" => true,
            "message" => $message,
            "code" => 200,
            "data" => $data,
        ])->setStatusCode(200);
    }

    public static function Error(string $message, mixed $data): JsonResponse
    {
        return response()->json([
            "success" => false,
            "message" => $message,
            "code" => 400,
            "data" => $data
        ])->setStatusCode(400);
    }

    public static function ServerError(string $message): JsonResponse
    {
        return response()->json([
            "success" => false,
            "message" => $message,
            "code" => 500,
            "data" => null
        ])->setStatusCode(500);
    }

    public static function NotFound(string $message): JsonResponse
    {
        return response()->json([
            "success" => false,
            "message" => $message,
            "code" => 404,
            "data" => null
        ])->setStatusCode(404);
    }

    public static function Custom(bool $status, string $message, mixed $data, int $code): JsonResponse
    {
        return response()->json([
            "success" => $status,
            "message" => $message,
            "code" => $code,
            "data" => $data
        ])->setStatusCode($code);
    }

    public static function Paginate(string $message, mixed $data, mixed $paginate): JsonResponse
    {
        return response()->json([
            "success" => true,
            "message" => $message,
            "data" => $data,
            "code" => 200,
            "pagination" => $paginate
        ])->setStatusCode(200);
    }

    public static function Create(string $message, mixed $data): JsonResponse
    {
        return response()->json([
            "success" => true,
            "message" => $message,
            "code" => 201,
            "data" => $data
        ])->setStatusCode(201);
    }

    public static function Forbidden(string $message = 'Akses ditolak'): JsonResponse
    {
        return response()->json([
            "success" => false,
            "message" => $message,
            "data" => null,
            "code" => 403,
        ])->setStatusCode(403);
    }

    public static function Unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            "success" => false,
            "message" => $message,
            "code"    => 401,
            "data"    => null,
        ])->setStatusCode(401);
    }
}
