<?php

namespace App\Base;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ResponseHelper
 * @package App\Base
 */
class ResponseHelper {

    /**
     * Help to generate JsonResponse with specified message and status code.
     * @param Message $message
     * @param int $httpStatusCode
     * @return JsonResponse
     */
    public static function jsonResponse(Message $message, int $httpStatusCode) : JsonResponse {
        return new JsonResponse($message , $httpStatusCode);
    }

}
