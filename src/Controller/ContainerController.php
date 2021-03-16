<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\EntityColumnsEnum;
use App\Constant\ErrorConstants;
use App\CycloBranch\AbstractCycloBranch;
use App\CycloBranch\BlockCycloBranch;
use App\CycloBranch\BlockMergeFormulaCycloBranch;
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
use App\Structure\AbstractStructure;
use App\Structure\BlockStructure;
use App\Structure\CollaboratorStructure;
use App\Structure\CollaboratorTransformed;
use App\Structure\ConcreateContainer;
use App\Structure\ModificationStructure;
use App\Structure\NewContainerStructure;
use App\Structure\NewContainerTransformed;
use App\Structure\SequenceStructure;
use App\Structure\UpdateContainerStructure;
use App\Structure\UpdateContainerTransformed;
use Doctrine\ORM\EntityManagerInterface;
use JsonMapper;
use JsonMapper_Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Swagger\Annotations as SWG;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

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
     * @param Request $request
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
     */
    public function index(UserRepository $userRepository, Request $request, Security $security) {
        return new JsonResponse($userRepository->findContainersForLoggedUser($security->getUser()->getId(), RequestHelper::getSorting($request)));
    }

    /**
     * Return containers which is free to read
     * @Route("/rest/free/container", name="container_free", methods={"GET"})
     * @param ContainerRepository $containerRepository
     * @param Request $request
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Container"},
     *     @SWG\Response(response="200", description="Return list of public containers."),
     * )
     */
    public function freeContainers(ContainerRepository $containerRepository, Request $request) {
        return new JsonResponse($containerRepository->findBy([EntityColumnsEnum::CONTAINER_VISIBILITY => ContainerVisibilityEnum::PUBLIC], RequestHelper::getSorting($request)->asArray()));
    }

    /**
     * Return container detail
     * @Route("/rest/container/{containerId}", name="container_id", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param Request $request
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
     *     @SWG\Response(response="403", description="Return when user has not enought permissions"),
     *     @SWG\Response(response="404", description="Return when container is not found."),
     * )
     */
    public function containerId(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->concreteContainer($container);
        if (!$modelMessage->result) {
            return ResponseHelper::jsonResponse($modelMessage);
        }
        $ccContainer = new ConcreateContainer($container->getId(), $container->getContainerName(), $container->getVisibility(), $model->concreteContainerCollaborators($container->getId(), RequestHelper::getSorting($request)));
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
     *          description="mode - permisions for new user",
     *          @SWG\Schema(type="string",
     *              example="{""mode"":""RW""}"),
     *      ),
     *     @SWG\Response(response="201", description="Create new container."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when user doesn't have enought permissions."),
     *     @SWG\Response(response="404", description="Return when container or user not found."),
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
     *     @SWG\Response(response="403", description="Return when permissions is insuficient."),
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
     * @SWG\Put(
     *     tags={"Collaborator"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="mode - permisions for user",
     *          @SWG\Schema(type="string",
     *              example="{""mode"":""RW""}"),
     *      ),
     *     @SWG\Response(response="204", description="Sucessfully removed collaborator."),
     *     @SWG\Response(response="400", description="Return when input is bad"),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permissions is insuficient."),
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
     *
     * @SWG\Get(
     *     tags={"Export"},
     *     @SWG\Response(response="200", description="Export modifications."),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="404", description="Not found."),
     * )
     *
     */
    public function modificationExport(Container $container, ModificationRepository $repository) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC || $this->isGranted("ROLE_USER")) {
            $export = new ModificationCycloBranch($repository, $container->getId());
            return $export->export();
        } else {
            return new JsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
        }
    }

    /**
     * Export blocks for CycloBranch
     * @Route("/rest/container/{containerId}/block/export", name="block_export", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param BlockRepository $repository
     * @return Response
     *
     * @SWG\Get(
     *     tags={"Export"},
     *     @SWG\Response(response="200", description="Export blocks."),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="404", description="Not found."),
     * )
     *
     */
    public function blockExport(Container $container, BlockRepository $repository) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC || $this->isGranted("ROLE_USER")) {
            $export = new BlockCycloBranch($repository, $container->getId());
            return $export->export();
        } else {
            return new JsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
        }
    }

    /**
     * Export sequences for CycloBranch
     * @Route("/rest/container/{containerId}/sequence/export", name="sequence_export", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param SequenceRepository $repository
     * @return Response
     *
     * @SWG\Get(
     *     tags={"Export"},
     *     @SWG\Response(response="200", description="Export sequences."),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="404", description="Not found."),
     * )
     *
     */
    public function sequenceExport(Container $container, SequenceRepository $repository) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC || $this->isGranted("ROLE_USER")) {
            $export = new SequenceCycloBranch($repository, $container->getId());
            return $export->export();
        } else {
            return new JsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
        }
    }

    /**
     * Export blocks for CycloBranch
     * @Route("/rest/container/{containerId}/block/export/merge", name="block_merge__export", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param BlockRepository $repository
     * @return Response
     *
     * @SWG\Get(
     *     tags={"Export"},
     *     @SWG\Response(response="200", description="Export merged blocks."),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="404", description="Not found."),
     * )
     *
     */
    public function blockMergeExport(Container $container, BlockRepository $repository) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC || $this->isGranted("ROLE_USER")) {
            $export = new BlockMergeFormulaCycloBranch($repository, $container->getId());
            return $export->export();
        } else {
            return new JsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS));
        }
    }

    /**
     * Export all for CycloBranch in zip file
     * @Route("/rest/container/{containerId}/export", name="all_merge__export", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param BlockRepository $blockRepository
     * @param SequenceRepository $sequenceRepository
     * @param ModificationRepository $modificationRepository
     * @return Response
     *
     * @SWG\Get(
     *     tags={"Export"},
     *     @SWG\Response(response="200", description="Export all."),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="404", description="Not found."),
     * )
     */
    public function allExport(Container $container, BlockRepository $blockRepository, SequenceRepository $sequenceRepository, ModificationRepository $modificationRepository) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC || $this->isGranted("ROLE_USER")) {
            return new StreamedResponse(function () use ($container, $blockRepository, $sequenceRepository, $modificationRepository) {
                $options = new Archive();
                $options->setSendHttpHeaders(true);
                $zip = new ZipStream('archive.zip', $options);

                $export = new ModificationCycloBranch($modificationRepository, $container->getId());
                $zip->addFile('modifications.txt', $export->download());

                $export = new BlockCycloBranch($blockRepository, $container->getId());
                $zip->addFile('blocks.txt', $export->download());

                $export = new BlockMergeFormulaCycloBranch($blockRepository, $container->getId());
                $zip->addFile('blocks_merge.txt', $export->download());

                $export = new SequenceCycloBranch($sequenceRepository, $container->getId());
                $zip->addFile('sequences.txt', $export->download());
                $zip->finish();
            });
        } else {
            return new JsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS));
        }
    }

    /**
     * Import modifications from CycloBranch
     * @Route("/rest/container/{containerId}/modification/import", name="modification_import", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Request $request
     * @param ModificationRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return Response
     *
     * @SWG\Post(
     *     tags={"Import"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Array with modification to import",
     *          @SWG\Schema(type="string",
     *              example="[{""modificationName"":""Acetyl"",""formula"":""H2C2O"",""mass"":42.0105646863,""nTerminal"":true,""cTerminal"":false},{""modificationName"":""Amidated"",""formula"":""HNO-1"",""mass"":-0.9840155848,""nTerminal"":false,""cTerminal"":false},{""modificationName"":""Ethanolamine"",""formula"":""H5C2N"",""mass"":43.0421991657,""nTerminal"":false,""cTerminal"":false},{""modificationName"":""Formyl"",""formula"":""CO"",""mass"":27.9949146221,""nTerminal"":true,""cTerminal"":false}]"),
     *      ),
     *     @SWG\Response(response="200", description="Return list of not imported modifications."),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="404", description="Not found."),
     * )
     *
     */
    public function modificationImport(Container $container, Request $request, ModificationRepository $repository, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        return $this->import($container, $request, new ModificationCycloBranch($repository, $container->getId()), ModificationStructure::class, $entityManager, $security, $logger);
    }

    /**
     * Import blocks from CycloBranch
     * @Route("/rest/container/{containerId}/block/import", name="block_import", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return Response
     *
     * @SWG\Post(
     *     tags={"Import"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="200", description="Return list of not imported blocks."),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="404", description="Not found."),
     * )
     */
    public function blockImport(Container $container, Request $request, BlockRepository $repository, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        return $this->import($container, $request, new BlockCycloBranch($repository, $container->getId()), BlockStructure::class, $entityManager, $security, $logger);
    }

    /**
     * Import sequences from CycloBranch
     * @Route("/rest/container/{containerId}/sequence/import", name="sequence_import", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Request $request
     * @param SequenceRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return Response
     *
     * @SWG\Post(
     *     tags={"Import"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="200", description="Return list of not imported sequences."),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="404", description="Not found."),
     * )
     */
    public function sequenceImport(Container $container, Request $request, SequenceRepository $repository, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        return $this->import($container, $request, new SequenceCycloBranch($repository, $container->getId()), SequenceStructure::class, $entityManager, $security, $logger);
    }

    /**
     * Clone container
     * @Route("/rest/container/{containerId}/clone", name="container_clone", methods={"POST"}, requirements={"containerId"="\d+"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("sequence", expr="repository.find(sequenceId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
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
     *     @SWG\Response(response="204", description="Sucessfully deleted sequence."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when sequence is not found.")
     * )
     */
    public function cloneContainer(Container $container, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC || $model->hasContainer($container->getId())) {
            return ResponseHelper::jsonResponse($model->cloneContainer($container));
        } else {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
        }
    }

    private function import(Container $container, Request $request, AbstractCycloBranch $import, $className, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        if ($model->hasContainerRW($container->getId())) {
            $mapper = new JsonMapper();
            $errorStack = [];
            $okStack = [];
            try {
                /** @var AbstractStructure[] $arItems */
                $arItems = $mapper->mapArray(json_decode($request->getContent()), [], $className);
                foreach ($arItems as $importItem) {
                    $result = $importItem->checkInput();
                    if (!$result->result) {
                        $importItem->error = 'ERROR: ' . $result->messageText;
                        array_push($errorStack, $importItem);
                    } else {
                        array_push($okStack, $importItem->transform());
                    }
                }
            } catch (JsonMapper_Exception $e) {
                return new JsonResponse(ErrorConstants::ERROR_JSON_FORMAT);
            }
            $errorStack = $import->import($container, $entityManager, $okStack, $errorStack);
            return new JsonResponse($errorStack, Response::HTTP_OK);
        } else {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
        }
    }

}
