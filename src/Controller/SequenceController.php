<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\ErrorConstants;
use App\Entity\Container;
use App\Entity\Sequence;
use App\Enum\ContainerVisibilityEnum;
use App\Model\ContainerModel;
use App\Repository\SequenceRepository;
use App\Structure\BlockExport;
use App\Structure\SequenceExport;
use App\Structure\SequenceStructure;
use App\Structure\SequenceTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\Security\Core\Security;

class SequenceController extends AbstractController {

    /**
     * Add new sequence for logged user
     * @Route("/rest/container/{containerId}/sequence", name="sequence_new", methods={"POST"})
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
     *     tags={"Sequence"},
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
     *              example="4545"),
     *      ),
     *     @SWG\Response(response="201", description="Create new sequence."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient.")
     * )
     */
    public function addNewSequence(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var SequenceTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new SequenceStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->createNewSequence($container, $trans);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Return sequences for logged user
     * @Route("/rest/container/{containerId}/sequence", name="sequence", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @param SequenceRepository $sequenceRepository
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Sequence"},
     *     @SWG\Response(response="200", description="Return list of blocks in container."),
     *     @SWG\Response(response="401", description="Return when user has not acces to container."),
     *     @SWG\Response(response="404", description="Return when sequence not found."),
     * )
     */
    public function index(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger, SequenceRepository $sequenceRepository) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC) {
            return new JsonResponse($sequenceRepository->findSequences($container->getId(), RequestHelper::getSorting($request)), Response::HTTP_OK);
        } else {
            if ($security->getUser() !== null) {
                $containerModel = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
                if ($containerModel->hasContainer($container->getId())) {
                    return new JsonResponse($sequenceRepository->findSequences($container->getId(), RequestHelper::getSorting($request)), Response::HTTP_OK);
                } else {
                    return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
                }
            } else {
                return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_UNAUTHORIZED));
            }
        }
    }

    /**
     * Delete sequence
     * @Route("/rest/container/{containerId}/sequence/{sequenceId}", name="sequence_delete", methods={"DELETE"}, requirements={"sequenceId"="\d+"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("sequence", expr="repository.find(sequenceId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Sequence $sequence
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Delete(
     *     tags={"Sequence"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Sucessfully deleted sequence."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when sequence is not found.")
     * )
     */
    public function deleteSequence(Container $container, Sequence $sequence, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->deleteSequence($container, $sequence);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Edit sequence
     * @Route("/rest/container/{containerId}/sequence/{sequenceId}", name="sequence_edit", methods={"PUT"}, requirements={"sequenceId"="\d+"})
     * @IsGranted("ROLE_USER")
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("sequence", expr="repository.find(sequenceId)")
     * @param Container $container
     * @param Sequence $sequence
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Sequence"},
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
     *              example="4545"),
     *      ),
     *     @SWG\Response(response="201", description="Edit sequence success."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient.")
     * )
     */
    public function editSequence(Container $container, Sequence $sequence, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var SequenceTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new SequenceStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->editSequence($container, $trans, $sequence);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Get sequence
     * @Route("/rest/container/{containerId}/sequence/{sequenceId}", name="sequence_detail", methods={"GET"}, requirements={"sequenceId"="\d+"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("sequence", expr="repository.find(sequenceId)")
     * @param Container $container
     * @param Sequence $sequence
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Delete(
     *     tags={"Sequence"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="200", description="Sucessfully found sequence."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when sequence is not found.")
     * )
     */
    public function detailSequence(Container $container, Sequence $sequence, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC) {
            return new JsonResponse($this->getSequenceData($sequence), Response::HTTP_OK);
        } else {
            if ($security->getUser() !== null && $this->isGranted("ROLE_USER")) {
                $containerModel = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
                if ($containerModel->hasContainer($container->getId()) && $container->getId() === $sequence->getContainer()->getId()) {
                    return new JsonResponse($this->getSequenceData($sequence), Response::HTTP_OK);
                } else {
                    return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
                }
            } else {
                return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_UNAUTHORIZED));
            }
        }
    }

    /**
     * Clone sequence
     * @Route("/rest/container/{containerId}/sequence/{sequenceId}/clone", name="sequence_clone", methods={"POST"}, requirements={"sequenceId"="\d+"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("sequence", expr="repository.find(sequenceId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Sequence $sequence
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Delete(
     *     tags={"Sequence"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Sucessfully deleted sequence."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when sequence is not found.")
     * )
     */
    public function cloneSequence(Container $container, Sequence $sequence, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        return $model->cloneSequence($container, $sequence);
    }

    private function getSequenceData(Sequence $sequence) {
        $sequenceExport = new SequenceExport();
        $sequenceExport->sequenceName = $sequence->getSequenceName();
        $sequenceExport->sequenceType = $sequence->getSequenceType();
        $sequenceExport->sequence = $sequence->getSequence();
        $sequenceExport->sequenceOriginal = $sequence->getSequenceOriginal();
        $sequenceExport->smiles = $sequence->getSequenceSmiles();
        $sequenceExport->uniqueSmiles = $sequence->getSequenceSmiles();
        $sequenceExport->formula = $sequence->getSequenceFormula();
        $sequenceExport->mass = $sequence->getSequenceMass();
        $sequenceExport->decays = $sequence->getDecays();
        $sequenceExport->source = $sequence->getSource();
        $sequenceExport->identifier = $sequence->getIdentifier();
        $sequenceExport->nModification = $sequence->getNModification();
        $sequenceExport->cModification = $sequence->getCModification();
        $sequenceExport->bModification = $sequence->getBModification();
        foreach ($sequence->getS2families() as $s2f) {
            array_push($sequenceExport->family, $s2f->getFamily());
        }
        $length = 0;
        foreach ($sequence->getB2s() as $b2s) {
            $block = $b2s->getBlock();
            $blockExport = new BlockExport();
            $blockExport->id = $block->getId();
            $blockExport->originalId = $b2s->getBlockOriginalId();
            $blockExport->blockName = $block->getBlockName();
            $blockExport->acronym = $block->getAcronym();
            $blockExport->formula = $block->getResidue();
            $blockExport->mass = $block->getBlockMass();
            $blockExport->smiles = $block->getBlockSmiles();
            $blockExport->uniqueSmiles = $block->getUsmiles();
            $blockExport->source = $block->getSource();
            $blockExport->identifier = $block->getIdentifier();
            $blockExport->losses = $block->getLosses();
            array_push($sequenceExport->blocks, $blockExport);
            $length++;
        }
        for ($i = 1; $i < $length; $i++) {
            for ($j = 0 ; $j < $i; $j++) {
                if ($sequenceExport->blocks[$i]->id === $sequenceExport->blocks[$j]->id) {
                    $sequenceExport->blocks[$i]->sameAs = $j;
                    break;
                }
            }
        }
        return $sequenceExport;
    }

}
