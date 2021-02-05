<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\ContainerVisibilityEnum;
use App\Constant\ErrorConstants;
use App\Entity\Container;
use App\Entity\Modification;
use App\Model\ContainerModel;
use App\Structure\ModificationStructure;
use App\Structure\ModificationTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Swagger\Annotations as SWG;

class ModificationController extends AbstractController {

    /**
     * Return modifications for logged user
     * @Route("/rest/container/{containerId}/modification", name="modification", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function getModifications(Container $container, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        if ($container->getVisibility() === ContainerVisibilityEnum::PRIVATE) {
            $modelMessage = $model->concreteContainer($container);
            if (!$modelMessage->result) {
                return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_NOT_FOUND));
            }
        }
        return new JsonResponse($model->getContainerModifications($container->getId()));
    }

    /**
     * Return modifications for logged user
     * @Route("/rest/container/{containerId}/modification", name="modification", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Modification"},
     *     @SWG\Response(response="200", description="Return list of modifications in container."),
     *     @SWG\Response(response="401", description="Return when user has not acces to container."),
     *     @SWG\Response(response="404", description="Return when container not found."),
     * )
     */
    public function getModificationsFree(Container $container, EntityManagerInterface $entityManager, LoggerInterface $logger) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PRIVATE) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_NOT_FOUND));
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), null, $logger);
        return new JsonResponse($model->getContainerModifications($container->getId()));
    }

    /**
     * Delete modification
     * @Route("/rest/container/{containerId}/modification/{modificationId}", name="modification_delete", methods={"DELETE"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("modification", expr="repository.find(modificationId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Modification $modification
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Delete(
     *     tags={"Modification"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Sucessfully deleted container."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="404", description="Return when container is not found."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient.")
     * )
     */
    public function deleteModification(Container $container, Modification $modification, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->deleteModification($container, $modification);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Update modification values
     * @Route("/rest/container/{containerId}/modification/{modificationId}", name="modification_update", methods={"PUT"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("modification", expr="repository.find(modificationId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Modification $modification
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Put(
     *     tags={"Modification"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Paramas: blockName, acronym, formula, mass, losses, smiles, source, identifier.",
     *          @SWG\Schema(type="string",
     *              example=""),
     *      ),
     *     @SWG\Response(response="204", description="Sucessfully update container."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container is not found.")
     * )
     */
    public function updateModification(Container $container, Modification $modification, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var ModificationTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new ModificationStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->updateModification($trans, $container, $modification);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Add new modification for logged user
     * @Route("/rest/container/{containerId}/modification", name="modification_new", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Modification"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Paramas: blockName, acronym, formula, mass, losses, smiles, source, identifier.",
     *          @SWG\Schema(type="string",
     *              example=""),
     *      ),
     *     @SWG\Response(response="201", description="Create new container."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient.")
     * )
     */
    public function addNewBlock(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var ModificationTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new ModificationStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->createNewModification($container, $trans);
        return ResponseHelper::jsonResponse($modelMessage);
    }

}
