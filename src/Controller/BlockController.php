<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\Constants;
use App\Constant\ErrorConstants;
use App\Entity\Block;
use App\Entity\Container;
use App\Enum\ContainerVisibilityEnum;
use App\Model\ContainerModel;
use App\Repository\BlockRepository;
use App\Smiles\SmilesHelper;
use App\Structure\BlockSmiles;
use App\Structure\BlockStructure;
use App\Structure\BlockTransformed;
use App\Structure\UniqueSmilesStructure;
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

    /**
     * Return containers for logged user
     * @Route("/rest/container/{containerId}/block", name="block", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @param BlockRepository $blockRepository
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Block"},
     *     @SWG\Response(response="200", description="Return list of blocks in container."),
     *     @SWG\Response(response="403", description="Return when user has not acces to container."),
     *     @SWG\Response(response="404", description="Return when container not found."),
     * )
     */
    public function index(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger, BlockRepository $blockRepository) {
        $possibleFilters = ['id', 'blockName', 'acronym', 'residue', 'blockMassFrom', 'blockMassTo', 'blockSmiles', 'losses', 'identifier', 'family'];
        $filters = RequestHelper::getFiltering($request, $possibleFilters);
        $filters = RequestHelper::transformIdentifier($filters);
        $sort = RequestHelper::getSorting($request);
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC) {
            return new JsonResponse($blockRepository->findBlocks($container->getId(), $filters, $sort), Response::HTTP_OK);
        } else {
            if ($security->getUser() !== null) {
                $containerModel = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
                if ($containerModel->hasContainer($container->getId())) {
                    return new JsonResponse($blockRepository->findBlocks($container->getId(), $filters, $sort), Response::HTTP_OK);
                }
            }
        }
        return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_UNAUTHORIZED));
    }

    /**
     * Delete block
     * @Route("/rest/container/{containerId}/block/{blockId}", name="block_delete", methods={"DELETE"}, requirements={"blockId"="\d+"})
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
     *     @SWG\Response(response="204", description="Sucessfully deleted block."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container is not found.")
     * )
     */
    public function deleteBlock(Container $container, Block $block, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->deleteBlock($container, $block);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Update block
     * @Route("/rest/container/{containerId}/block/{blockId}", name="block_update", methods={"PUT"}, requirements={"blockId"="\d+"})
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
     *              example="{""blockName"": ""cyclohexane"", ""acronym"": ""Chx"", ""formula"": ""C6H12"", ""mass"": 84.093900, ""smiles"": ""C1CCCCC1"", ""source"": 0, ""identifier"": ""8078""}")
     *      ),
     *     @SWG\Response(response="204", description="Sucessfully update block."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container is not found.")
     * )
     */
    public function updateBlock(Container $container, Block $block, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
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
     * Add new block for logged user to container
     * @Route("/rest/container/{containerId}/block", name="block_new", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
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
     *              example="{""blockName"": ""cyclohexane"", ""acronym"": ""Chx"", ""formula"": ""C6H12"", ""mass"": 84.093900, ""smiles"": ""C1CCCCC1"", ""source"": 0, ""identifier"": ""8078""}")
     *     ),
     *     @SWG\Response(response="201", description="Create new container."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when not found container.")
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
        return new JsonResponse($modelMessage, $modelMessage->status, Constants::getLocation('container/' . $container->getId() . '/block/', $modelMessage->id));
    }

    /**
     * Block detail
     * @Route("/rest/container/{containerId}/block/{blockId}", name="block_detail", methods={"GET"}, requirements={"blockId"="\d+"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("block", expr="repository.find(blockId)")
     * @param Container $container
     * @param Block $block
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @SWG\Get(
     *     tags={"Block"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="200", description="Return block data."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container or block is not found.")
     * )
     */
    public function detailBlock(Container $container, Block $block, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC) {
            return new JsonResponse($block);
        } else if ($this->isGranted("ROLE_USER")) {
            $containerModel = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
            if ($containerModel->hasContainer($container->getId())) {
                return new JsonResponse($block);
            }
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
        }
        return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_NOT_FOUND));
    }

    /**
     * Block usage
     * @Route("/rest/container/{containerId}/block/{blockId}/usage", name="block_usage", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("block", expr="repository.find(blockId)")
     * @param Container $container
     * @param Block $block
     * @param Request $request
     * @param BlockRepository $blockRepository
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @SWG\Get(
     *     tags={"Block"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Return data of sequence with block use."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container or block is not found.")
     * )
     */
    public function usageBlock(Container $container, Block $block, Request $request, BlockRepository $blockRepository, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $possibleFilters = ['id', 'sequenceName', 'sequence', 'sequenceType', 'sequenceFormula', 'sequenceMassFrom', 'sequenceMassTo', 'nModification', 'cModification', 'bModification', 'identifier', 'family', 'organism', 'usagesFrom', 'usagesTo'];
        $filters = RequestHelper::getFiltering($request, $possibleFilters);
        $filters = RequestHelper::transformIdentifier($filters);
        $sort = RequestHelper::getSorting($request);
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC) {
            return new JsonResponse($blockRepository->blockUsage($container->getId(), $block->getId(), $filters, $sort));
        } else if ($this->isGranted("ROLE_USER")) {
            $containerModel = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
            if ($containerModel->hasContainer($container->getId())) {
                return new JsonResponse($blockRepository->blockUsage($container->getId(), $block->getId(), $filters, $sort));
            }
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
        }
        return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_NOT_FOUND));
    }

    /**
     * Return unique smiles and info for logged user
     * @Route("/rest/container/{containerId}/smiles", name="block_unique", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @param BlockRepository $blockRepository
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Block"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Paramas: array of object with smiles (string, required) and isPolyketide (boolean).",
     *          @SWG\Schema(type="string",
     *              example="[{""smiles"":""OC(C(C(CC)C)N)=O"",""isPolyketide"":false},{""smiles"":""NC(C(=O)O)C(C)CC"",""isPolyketide"":false},{""smiles"":""NCCCC(C(=O)O)N"",""isPolyketide"":false},{""smiles"":""NC(C(=O)O)CC1=CC=CC=C1"",""isPolyketide"":false},{""smiles"":""N1CCCC1C(=O)O"",""isPolyketide"":false},{""smiles"":""OC(=O)C(C(C)CC)NC(=O)C"",""isPolyketide"":false}]")
     *     ),
     *     @SWG\Response(response="200", description="Return list of blocks in container."),
     *     @SWG\Response(response="403", description="Return when user has not acces to container."),
     *     @SWG\Response(response="404", description="Return when container not found."),
     * )
     */
    public function smiles(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger, BlockRepository $blockRepository) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC) {
            return $this->smilesNext($container, $request, $blockRepository);
        } else {
            if ($security->getUser() !== null) {
                $containerModel = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
                if ($containerModel->hasContainer($container->getId())) {
                    return $this->smilesNext($container, $request, $blockRepository);
                }
            }
        }
        return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
    }

    function smilesNext(Container $container, Request $request, BlockRepository $blockRepository) {
        $smilesInput = SmilesHelper::checkInputJson($request);
        if ($smilesInput instanceof JsonResponse) {
            return $smilesInput;
        }
        $length = count($smilesInput);
        $nextCheck = SmilesHelper::checkNext($smilesInput, $length);
        if ($nextCheck instanceof JsonResponse) {
            return $nextCheck;
        }
        $smiles = SmilesHelper::unique($smilesInput, $length);
        /** @var UniqueSmilesStructure $smile */
        foreach ($smiles as $smile) {
            $block = $blockRepository->findOneBy(['container' => $container->getId(), 'usmiles' => $smile->unique]);
            if ($block === null) {
                $smile->block = null;
                continue;
            }
            $blockSmiles = new BlockSmiles();
            $blockSmiles->databaseId = $block->getId();
            $blockSmiles->structureName = $block->getBlockName();
            $blockSmiles->formula = $block->getResidue();
            $blockSmiles->mass = $block->getBlockMass();
            $blockSmiles->smiles = $block->getBlockSmiles();
            $blockSmiles->database = $block->getSource();
            $blockSmiles->identifier = $block->getIdentifier();
            $smile->acronym = $block->getAcronym();
            $smile->block = $blockSmiles;
        }
        return new JsonResponse($smiles);
    }

}
