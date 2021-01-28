<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\ResponseHelper;
use App\Constant\ContainerVisibilityEnum;
use App\Constant\ErrorConstants;
use App\Entity\Container;
use App\Model\ContainerModel;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\Security\Core\Security;

class ModificationController extends AbstractController {

    /**
     * Return containers for logged user
     * @Route("/rest/container/{id}/modification", name="modification", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Container"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="200", description="Return list of containers for logged user."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Swagger(
     *      @SWG\SecurityScheme(type="apiKey", securityDefinition="ApiKeyAuth", in="header", name="X-AUTH-TOKEN")
     *     )
     * )
     */
    public function getModifications(Container $container, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        if ($container->getVisibility() === ContainerVisibilityEnum::PRIVATE) {
            $modelMessage = $model->concreteContainer($container);
            if (!$modelMessage->result) {
                return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER));
            }
        }
        return new JsonResponse($model->getContainerModifications($container->getId()));
    }

    /**
     * Return containers for logged user
     * @Route("/rest/container/{id}/modification", name="modification", methods={"GET"})
     * @param Container $container
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function getModificationsFree(Container $container, EntityManagerInterface $entityManager, LoggerInterface $logger) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PRIVATE) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER));
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), null, $logger);
        return new JsonResponse($model->getContainerModifications($container->getId()));
    }

}
