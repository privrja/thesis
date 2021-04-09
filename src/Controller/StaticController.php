<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\EntityColumnsEnum;
use App\Constant\ErrorConstants;
use App\Entity\Container;
use App\Enum\ContainerVisibilityEnum;
use App\Model\ContainerModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticController {

    static function containerGetData(Container $container, Request $request, ServiceEntityRepository $repository, ManagerRegistry $doctrine, EntityManagerInterface $entityManager, $user, LoggerInterface $logger, string $callOk): JsonResponse {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC) {
            return self::$callOk($container, $request, $repository);
        } else {
            if ($user !== null) {
                $containerModel = new ContainerModel($entityManager, $doctrine, $user, $logger);
                if ($containerModel->hasContainer($container->getId())) {
                    return self::$callOk($container, $request, $repository);
                }
            }
        }
        return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_FORBIDDEN));
    }

    static function findData(Container $container, Request $request, ServiceEntityRepository $repository) {
        return new JsonResponse($repository->findBy([EntityColumnsEnum::CONTAINER => $container->getId()], RequestHelper::getSorting($request)->asArray()), Response::HTTP_OK);
    }

}
