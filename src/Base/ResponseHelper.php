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
     * @param int $httpStatusCode
     * @return JsonResponse
     */
    public static function jsonResponse(Message $message = null, int $httpStatusCode = Response::HTTP_NOT_FOUND) : JsonResponse {
        return new JsonResponse($message , $httpStatusCode);
    }

}
