<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\EntityColumnsEnum;
use App\Constant\ErrorConstants;
use App\CycloBranch\BlockCycloBranch;
use App\CycloBranch\ModificationCycloBranch;
use App\CycloBranch\SequenceCycloBranch;
use App\Entity\Container;
use App\Entity\User;
use App\Enum\ContainerVisibilityEnum;
use App\Model\ContainerModel;
use App\Repository\BlockRepository;
use App\Repository\ContainerRepository;
use App\Repository\ModificationRepository;
use App\Repository\SequenceRepository;
use App\Repository\UserRepository;
use App\Structure\CollaboratorStructure;
use App\Structure\CollaboratorTransformed;
use App\Structure\ConcreateContainer;
use App\Structure\NewContainerStructure;
use App\Structure\NewContainerTransformed;
use App\Structure\UpdateContainerStructure;
use App\Structure\UpdateContainerTransformed;
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

/**
 * Class ContainerController
 * @package App\Controller
 */
class ContainerController extends AbstractController {

    /**
     * Return containers for logged user
     * @Route("/rest/container", name="container", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param UserRepository $userRepository
     * @param Security $security
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
     *
     */
    public function index(UserRepository $userRepository, Security $security) {
        // TODO prepare sorting and filtering options to query, maybe paging
        return new JsonResponse($userRepository->findContainersForLoggedUser($security->getUser()->getId()));
    }

    /**
     * Return containers which is free to read
     * @Route("/rest/free/container", name="container_free", methods={"GET"})
     * @param ContainerRepository $containerRepository
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Container"},
     *     @SWG\Response(response="200", description="Return list of public containers."),
     * )
     *
     */
    public function freeContainers(ContainerRepository $containerRepository) {
        // TODO prepare sorting and filtering options to query, maybe paging
        return new JsonResponse($containerRepository->findBy([EntityColumnsEnum::CONTAINER_VISIBILITY => ContainerVisibilityEnum::PUBLIC]));
    }

    /**
     * Return containers for logged user
     * @Route("/rest/container/{containerId}", name="container_id", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @Entity("container", expr="repository.find(containerId)")
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
     *     @SWG\Response(response="200", description="Return specific container for user."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="404", description="Return when container is not found."),
     * )
     *
     */
    public function containerId(Container $container, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->concreteContainer($container);
        if (!$modelMessage->result) {
            return ResponseHelper::jsonResponse($modelMessage);
        }
        $ccContainer = new ConcreateContainer($container->getId(), $container->getContainerName(), $container->getVisibility(), $model->concreteContainerCollaborators($container->getId()));
        return new JsonResponse($ccContainer, Response::HTTP_OK);
    }

    /**
     * Add new container for logged user
     * @Route("/rest/container", name="container_new", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Container"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Two paramas: name and visibility. At least one shouldn't be empty. Visibility has values: PRIVATE or PUBLIC.",
     *          @SWG\Schema(type="string",
     *              example="{""containerName"":""ContainerName"",""visibility"":""PRIVATE""}"),
     *      ),
     *     @SWG\Response(response="201", description="Create new container."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     * )
     *
     */
    public function addNewContainer(Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var NewContainerTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new NewContainerStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->createNew($trans);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Delete container with all content -> delete all blocks, sequences, modifications, etc.
     * @Route("/rest/container/{containerId}", name="container_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Delete(
     *     tags={"Container"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Sucessfully deleted container."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="404", description="Return when container is not found.")
     * )
     *
     */
    public function deleteContainer(Container $container, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->delete($container);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Update container values (name, visibility)
     * @Route("/rest/container/{containerId}", name="container_update", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Put(
     *     tags={"Container"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Two paramas: name and visibility. At least one shouldn't be empty. Visibility has values: PRIVATE or PUBLIC.",
     *          @SWG\Schema(type="string",
     *              example="{""name"":""ContainerName"",""visibility"":""PRIVATE""}"),
     *      ),
     *     @SWG\Response(response="204", description="Sucessfully update container."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="404", description="Return when container is not found.")
     * )
     *
     */
    public function updateContainer(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var UpdateContainerTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new UpdateContainerStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->update($trans, $container);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Add new user to container
     * @Route("/rest/container/{containerId}/collaborator/{userId}", name="collaborator_new", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("collaborator", expr="repository.find(userId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param User $collaborator
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Collaborator"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="",
     *          @SWG\Schema(type="string",
     *              example=""),
     *      ),
     *     @SWG\Response(response="201", description="Create new container."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when user doesn't have enought permissions.")
     * )
     */
    public function addNewCollaborator(Container $container, User $collaborator, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var CollaboratorTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new CollaboratorStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->createNewCollaborator($collaborator, $container, $trans);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Remove collaborator from container.
     * @Route("/rest/container/{containerId}/collaborator/{userId}", name="collaborator_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("collaborator", expr="repository.find(userId)")
     * @param Container $container
     * @param User $collaborator
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Delete(
     *     tags={"Collaborator"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Sucessfully removed collaborator."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="404", description="Return when container is not found.")
     * )
     */
    public function deleteCollaborator(Container $container, User $collaborator, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->deleteCollaborator($collaborator, $container);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Edit collaborator from in container.
     * @Route("/rest/container/{containerId}/collaborator/{userId}", name="collaborator_update", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("collaborator", expr="repository.find(userId)")
     * @param Container $container
     * @param User $collaborator
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Delete(
     *     tags={"Collaborator"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Sucessfully removed collaborator."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="404", description="Return when container is not found.")
     * )
     */
    public function updateCollaborator(Container $container, User $collaborator, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var CollaboratorTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new CollaboratorStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->updateCollaborator($collaborator, $container, $trans);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Export modifications for CycloBranch
     * @Route("/rest/container/{containerId}/modification/export", name="modification_export", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param ModificationRepository $repository
     * @return Response
     */
    public function modificationExport(Container $container, ModificationRepository $repository) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC || $this->isGranted("ROLE_USER")) {
            $export = new ModificationCycloBranch($repository, $container->getId());
            return $export->export();
        } else {
            return new JsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS));
        }
    }

    /**
     * Export blocks for CycloBranch
     * @Route("/rest/container/{containerId}/block/export", name="block_export", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param BlockRepository $repository
     * @return Response
     */
    public function blockExport(Container $container, BlockRepository $repository) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC || $this->isGranted("ROLE_USER")) {
            $export = new BlockCycloBranch($repository, $container->getId());
            return $export->export();
        } else {
            return new JsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS));
        }
    }

    /**
     * Export blocks for CycloBranch
     * @Route("/rest/container/{containerId}/sequence/export", name="sequence_export", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param SequenceRepository $repository
     * @return Response
     */
    public function sequenceExport(Container $container, SequenceRepository $repository) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC || $this->isGranted("ROLE_USER")) {
            $export = new SequenceCycloBranch($repository, $container->getId());
            return $export->export();
        } else {
            return new JsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS));
        }
    }

}
