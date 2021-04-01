<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\Constants;
use App\Constant\ErrorConstants;
use App\Entity\Container;
use App\Entity\Sequence;
use App\Enum\ContainerVisibilityEnum;
use App\Model\ContainerModel;
use App\Repository\SequenceRepository;
use App\Structure\BlockExport;
use App\Structure\SequenceExport;
use App\Structure\SequencePatchStructure;
use App\Structure\SequencePatchTransformed;
use App\Structure\SequenceStructure;
use App\Structure\SequenceTransformed;
use Doctrine\Common\Annotations\Annotation\Enum;
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
     *          description="Many params: sequenceName, formula, mass, smiles, source, identifier, sequence - string record with block acronyms, decays - array from SmilesDrawer with id of decays edges, sequenceOriginal - same as sequence, but with id of numeric blocks, sequenceType - values: linear, branched, cyclic, branch-cyclic, linear-polyketide, cyclic-polyketide, other, nModification - id of modification in database, or new modification to create, cModification, bModification, family - array with id of sequence famillies, or families to create, blocks - array of blocks - id for blocks in database, or new blocks to create",
     *          @SWG\Schema(type="string",
     *              example="{""sequenceName"":""pseudacyclin a"",""formula"":""C39H61N7O7"",""mass"":739.463247,""smiles"":""CCC(C)C1C(=O)NC(C(=O)NCCCC(C(=O)NC(C(=O)N2CCCC2C(=O)N1)CC3=CC=CC=C3)NC(=O)C(C(C)CC)NC(=O)C)C(C)CC"",""source"":0,""identifier"":""46848855"",""sequence"":""[Ile]-[Pro]-[Phe]\\([Orn]-[NAc-Ile]\\)[Ile]"",""decays"":""[6,10,17,21,28,37]"",""sequenceOriginal"":""[0]-[4]-[3]\\([2]-[5]\\)[1]"",""sequenceType"":""branch-cyclic"",""nModification"":null,""cModification"":null,""bModification"":null,""family"":[""4""],""blocks"":[{""databaseId"":15,""originalId"":0,""sameAs"":null,""acronym"":""Ile"",""blockName"":""Isoleucine"",""smiles"":""CCC(C)C(N)C(O)=O"",""formula"":""C6H11NO"",""mass"":113.084064,""source"":0,""identifier"":""6306""},{""databaseId"":15,""originalId"":1,""sameAs"":0,""acronym"":""Ile"",""blockName"":""Isoleucine"",""smiles"":""CCC(C)C(N)C(O)=O"",""formula"":""C6H11NO"",""mass"":113.084064,""source"":0,""identifier"":""6306""},{""databaseId"":39,""originalId"":2,""sameAs"":null,""acronym"":""Orn"",""blockName"":""Ornithine"",""smiles"":""NCCCC(N)C(O)=O"",""formula"":""C5H10N2O"",""mass"":114.079313,""source"":0,""identifier"":""389""},{""databaseId"":19,""originalId"":3,""sameAs"":null,""acronym"":""Phe"",""blockName"":""Phenylalanine"",""smiles"":""NC(CC1=CC=CC=C1)C(O)=O"",""formula"":""C9H9NO"",""mass"":147.068414,""source"":0,""identifier"":""6140""},{""databaseId"":9,""originalId"":4,""sameAs"":null,""acronym"":""Pro"",""blockName"":""Proline"",""smiles"":""OC(=O)C1CCCN1"",""formula"":""C5H7NO"",""mass"":97.052764,""source"":0,""identifier"":""145742""},{""databaseId"":26,""originalId"":5,""sameAs"":null,""acronym"":""NAc-Ile"",""blockName"":""N-Acetyl-Isoleucine"",""smiles"":""CCC(C)C(NC(C)=O)C(O)=O"",""formula"":""C8H13NO2"",""mass"":155.094629,""source"":0,""identifier"":""306109""}]}")
     *      ),
     *     @SWG\Response(response="201", description="Create new sequence."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container not found.")
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
        return new JsonResponse($modelMessage, $modelMessage->status, isset($modelMessage->id) ? Constants::getLocation('container/' . $container->getId() . '/sequence/', $modelMessage->id) : []);
    }

    /**
     * Return sequences from container
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
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when sequence not found."),
     * )
     */
    public function index(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger, SequenceRepository $sequenceRepository) {
        $possibleFilters = ['id', 'sequenceName', 'sequence', 'sequenceType', 'sequenceFormula', 'sequenceMassFrom', 'sequenceMassTo', 'nModification', 'cModification', 'bModification','identifier', 'family', 'organism'];
        $filters = RequestHelper::getFiltering($request, $possibleFilters);
        $filters = RequestHelper::transformIdentifier($filters);
        $sort = RequestHelper::getSorting($request);
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC) {
            return new JsonResponse($sequenceRepository->findSequences($container->getId(), $filters, $sort), Response::HTTP_OK);
        } else {
            if ($security->getUser() !== null) {
                $containerModel = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
                if ($containerModel->hasContainer($container->getId())) {
                    return new JsonResponse($sequenceRepository->findSequences($container->getId(), $filters, $sort), Response::HTTP_OK);
                } else {
                    return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
                }
            } else {
                return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_UNAUTHORIZED));
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
     *     @SWG\Response(response="404", description="Return when container or sequence not found.")
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
     * @SWG\Put(
     *     tags={"Sequence"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Many params: sequenceName, formula, mass, smiles, source, identifier, sequence - string record with block acronyms, decays - array from SmilesDrawer with id of decays edges, sequenceOriginal - same as sequence, but with id of numeric blocks, sequenceType - values: linear, branched, cyclic, branch-cyclic, linear-polyketide, cyclic-polyketide, other, nModification - id of modification in database, or new modification to create, cModification, bModification, family - array with id of sequence famillies, or families to create, blocks - array of blocks - id for blocks in database, or new blocks to create",
     *          @SWG\Schema(type="string",
     *              example="{""sequenceName"":""pseudacyclin a"",""formula"":""C39H61N7O7"",""mass"":739.463247,""smiles"":""CCC(C)C1C(=O)NC(C(=O)NCCCC(C(=O)NC(C(=O)N2CCCC2C(=O)N1)CC3=CC=CC=C3)NC(=O)C(C(C)CC)NC(=O)C)C(C)CC"",""source"":0,""identifier"":""46848855"",""sequence"":""[Ile]-[Pro]-[Phe]\\([Orn]-[NAc-Ile]\\)[Ile]"",""decays"":""[6,10,17,21,28,37]"",""sequenceOriginal"":""[0]-[4]-[3]\\([2]-[5]\\)[1]"",""sequenceType"":""branch-cyclic"",""nModification"":null,""cModification"":null,""bModification"":null,""family"":[""4""],""blocks"":[{""databaseId"":15,""originalId"":0,""sameAs"":null,""acronym"":""Ile"",""blockName"":""Isoleucine"",""smiles"":""CCC(C)C(N)C(O)=O"",""formula"":""C6H11NO"",""mass"":113.084064,""source"":0,""identifier"":""6306""},{""databaseId"":15,""originalId"":1,""sameAs"":0,""acronym"":""Ile"",""blockName"":""Isoleucine"",""smiles"":""CCC(C)C(N)C(O)=O"",""formula"":""C6H11NO"",""mass"":113.084064,""source"":0,""identifier"":""6306""},{""databaseId"":39,""originalId"":2,""sameAs"":null,""acronym"":""Orn"",""blockName"":""Ornithine"",""smiles"":""NCCCC(N)C(O)=O"",""formula"":""C5H10N2O"",""mass"":114.079313,""source"":0,""identifier"":""389""},{""databaseId"":19,""originalId"":3,""sameAs"":null,""acronym"":""Phe"",""blockName"":""Phenylalanine"",""smiles"":""NC(CC1=CC=CC=C1)C(O)=O"",""formula"":""C9H9NO"",""mass"":147.068414,""source"":0,""identifier"":""6140""},{""databaseId"":9,""originalId"":4,""sameAs"":null,""acronym"":""Pro"",""blockName"":""Proline"",""smiles"":""OC(=O)C1CCCN1"",""formula"":""C5H7NO"",""mass"":97.052764,""source"":0,""identifier"":""145742""},{""databaseId"":26,""originalId"":5,""sameAs"":null,""acronym"":""NAc-Ile"",""blockName"":""N-Acetyl-Isoleucine"",""smiles"":""CCC(C)C(NC(C)=O)C(O)=O"",""formula"":""C8H13NO2"",""mass"":155.094629,""source"":0,""identifier"":""306109""}]}")
     *      ),
     *     @SWG\Response(response="201", description="Update sequence."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container or sequence is not found.")
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
     * @SWG\Get(
     *     tags={"Sequence"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="200", description="Sucessfully found sequence."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container or sequence is not found.")
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

    /** Patch sequence
     * @Route("/rest/container/{containerId}/sequence/{sequenceId}", name="sequence_patch", methods={"PATCH"}, requirements={"sequenceId"="\d+"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("sequence", expr="repository.find(sequenceId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Sequence $sequence
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     *
     * @return SequencePatchTransformed|JsonResponse
     * @SWG\Get(
     *     tags={"Sequence"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="200", description="Sucessfully update sequence."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container or sequence is not found.")
     * )
     */
    public function patchSequence(Container $container, Sequence $sequence, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var SequencePatchTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new SequencePatchStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        return ResponseHelper::jsonResponse($model->patchSequence($container, $sequence, $trans));
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
     * @SWG\Post(
     *     tags={"Sequence"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Sucessfully deleted sequence."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container or sequence is not found.")
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
        foreach ($sequence->getS2Organism() as $s2o) {
            array_push($sequenceExport->organism, $s2o->getOrganism());
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
