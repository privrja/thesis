<?php

namespace App\Base;

use App\Constant\ErrorConstants;
use App\Structure\AbstractStructure;
use App\Structure\AbstractTransformed;
use InvalidArgumentException;
use JsonMapper;
use JsonMapper_Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RequestHelper {

    /**
     * Method that helps load JSON input, check it and transform it. If something is goes wrong it return JsonResponse with status 400. Otherwise return transformed data.
     * @param Request $request
     * @param AbstractStructure $structure
     * @param LoggerInterface $logger
     * @return AbstractTransformed|JsonResponse
     */
    public static function evaluateRequest(Request $request, AbstractStructure $structure, LoggerInterface $logger) {
        $mapper = new JsonMapper();
        try {
            $containerData = $mapper->map(json_decode($request->getContent()), $structure);
        } catch (JsonMapper_Exception | InvalidArgumentException  $e) {
            $logger->warning($e->getMessage(), $e->getTrace());
            return ResponseHelper::emplaceJsonResponse(ErrorConstants::ERROR_JSON_FORMAT);
        }
        $message = $containerData->checkInput();
        if(!$message->result) {
            return ResponseHelper::jsonResponse($message);
        }
        return $containerData->transform();
    }

}
