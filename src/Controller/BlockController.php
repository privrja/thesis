<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\ContainerVisibilityEnum;
use App\Constant\ErrorConstants;
use App\Entity\Block;
use App\Entity\Container;
use App\Model\ContainerModel;
use App\Repository\BlockRepository;
use App\Structure\BlockStructure;
use App\Structure\BlockTransformed;
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

class BlockController extends AbstractController {

    const CONTAINER = 'container';

    /**
     * Return containers for logged user
     * @Route("/rest/container/{id}/block", name="block", methods={"GET"})
     * @param Container $container
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Block"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="200", description="Return list of blocks in container."),
     *     @SWG\Response(response="401", description="Return when user has not acces to container."),
     *     @SWG\Response(response="404", description="Return when container not found."),
     *     @SWG\Swagger(
     *      @SWG\SecurityScheme(type="apiKey", securityDefinition="ApiKeyAuth", in="header", name="X-AUTH-TOKEN")
     *     )
     * )
     */
    public function index(Container $container, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger, BlockRepository $blockRepository) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC) {
            return new JsonResponse($blockRepository->findBy([self::CONTAINER => $container->getId()]), Response::HTTP_OK);
        } else {
            if ($security->getUser() !== null) {
                $containerModel = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
                if ($containerModel->hasContainer($container->getId())) {
                    return new JsonResponse($blockRepository->findBy([self::CONTAINER => $container->getId()]), Response::HTTP_OK);
                } else {
                    return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_NOT_FOUND));
                }
            } else {
                return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_UNAUTHORIZED));
            }
        }
    }

    /**
     * Delete block
     * @Route("/rest/container/{containerId}/block/{blockId}", name="block_delete", methods={"DELETE"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("block", expr="repository.find(blockId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Block $block
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Delete(
     *     tags={"Block"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Sucessfully deleted container."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="404", description="Return when container is not found.")
     * )
     */
    public function deleteBlock(Container $container, Block $block, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->deleteBlock($container, $block);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Update container values (name, visibility)
     * @Route("/rest/container/{containerId}/block/{blockId}", name="block_update", methods={"PUT"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("block", expr="repository.find(blockId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Block $block
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Put(
     *     tags={"Block"},
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
     *     @SWG\Response(response="404", description="Return when container is not found.")
     * )
     */
    public function updateBlock(Container $container, Block $block, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        var_dump($block->getId());
        var_dump($container->getId());
        /** @var BlockTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new BlockStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->updateBlock($trans, $container, $block);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Add new container for logged user
     * @Route("/rest/container/{id}/block", name="block_new", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * @param Container $container
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
     *          description="Paramas: blockName, acronym, formula, mass, losses, smiles, source, identifier.",
     *          @SWG\Schema(type="string",
     *              example=""),
     *      ),
     *     @SWG\Response(response="201", description="Create new container."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     * )
     */
    public function addNewBlock(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var BlockTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new BlockStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->createNewBlock($container, $trans);
        return ResponseHelper::jsonResponse($modelMessage);
    }

}
