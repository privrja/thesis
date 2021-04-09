<?php

namespace App\Base;

use App\Constant\ErrorConstants;
use App\Exception\IllegalStateException;
use App\Smiles\Parser\ReferenceParser;
use App\Structure\AbstractStructure;
use App\Structure\AbstractTransformed;
use App\Structure\Reference;
use App\Structure\Sort;
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
            return ResponseHelper::emplaceJsonResponse(ErrorConstants::ERROR_JSON_FORMAT . $e->getMessage());
        }
        $message = $containerData->checkInput();
        if (!$message->result) {
            return ResponseHelper::jsonResponse($message);
        }
        return $containerData->transform();
    }

    public static function getSorting(Request $request): Sort {
        $sort = $request->get('sort');
        if (!isset($sort)) {
            $sort = 'id';
        }
        $order = $request->get('order');
        if (!isset($order)) {
            $order = 'asc';
        }
        return new Sort($sort, $order);
    }

    public static function getFiltering(Request $request, array $possibleFilters) {
        $res = [];
        foreach ($possibleFilters as $filterParam) {
            $filterValue = $request->get($filterParam);
            if (isset($filterValue)) {
                $res[$filterParam] = $filterValue;
            }
        }
        return $res;
    }

    public static function transformFilters(array $filters, array $paramsToTransform, array $transformValues) {
        foreach ($paramsToTransform as $param) {
            if (isset($filters[$param]) && isset($transformValues[$filters[$param]])) {
                $filters[$param] = $transformValues[$filters[$param]];
            }
        }
        return $filters;
    }

    public static function transformIdentifier(array $filters) {
        if (isset($filters['identifier'])) {
            $value = $filters['identifier'];
            $refParser = new ReferenceParser();
            try {
                $refResult = $refParser->parse($value);
                if ($refResult->isAccepted()) {
                    /** @var Reference $result */
                    $result = $refResult->getResult();
                    if (isset($result->source) && isset($result->identifier)) {
                        $filters['source'] = $result->source;
                        $filters['identifier'] = $result->identifier;
                    } else {
                        unset($filters['identifier']);
                    }
                } else {
                    unset($filters['identifier']);
                }
            } catch (IllegalStateException $e) {
                unset($filters['identifier']);
            }
        }
        return $filters;
    }

}
