<?php

namespace App\Base;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResponseHelper
 * @package App\Base
 */
class ResponseHelper {

    /**
     * Help to generate JsonResponse with specified message and status code.
     * @param Message $message
     * @return JsonResponse
     */
    public static function jsonResponse(Message $message = null) : JsonResponse {
        return new JsonResponse($message , $message->status);
    }

    public static function emplaceJsonResponse(string $text, int $status = Response::HTTP_BAD_REQUEST): JsonResponse {
        return new JsonResponse(new Message($text), $status);
    }

}
