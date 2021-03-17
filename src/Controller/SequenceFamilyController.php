<?php

namespace App\Controller;

use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Entity\Container;
use App\Entity\SequenceFamily;
use App\Model\ContainerModel;
use App\Repository\SequenceFamilyRepository;
use App\Structure\FamilyStructure;
use App\Structure\FamilyTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Swagger\Annotations as SWG;

class SequenceFamilyController extends AbstractController {

    /**
     * Return sequence families for logged user
     * @Route("/rest/container/{containerId}/sequence/family", name="sequence_family", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @param SequenceFamilyRepository $sequenceFamilyRepository
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Sequence Family"},
     *     @SWG\Response(response="200", description="Return list of sequence families in container."),
     *     @SWG\Response(response="403", description="Return when user has not acces to container."),
     *     @SWG\Response(response="404", description="Return when container not found."),
     * )
     */
    public function index(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger, SequenceFamilyRepository $sequenceFamilyRepository) {
        return StaticController::containerGetData($container, $request, $sequenceFamilyRepository, $this->getDoctrine(), $entityManager, $security->getUser(), $logger, 'findData');
    }

    /**
     * Add new sequence family for logged user
     * @Route("/rest/container/{containerId}/sequence/family", name="sequence_family_new", methods={"POST"})
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
     *     tags={"Sequence Family"},
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
     *              example="{""family"": ""Peptides""}"),
     *      ),
     *     @SWG\Response(response="201", description="Create new sequence family."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container is not found.")
     * )
     */
    public function addNewBlock(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var FamilyTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new FamilyStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->createNewSequenceFamily($container, $trans);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Delete block family
     * @Route("/rest/container/{containerId}/sequence/family/{familyId}", name="sequence_family_delete", methods={"DELETE"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("sequenceFamily", expr="repository.find(familyId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param SequenceFamily $sequenceFamily
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Delete(
     *     tags={"Sequence Family"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Sucessfully deleted sequence family."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container or sequence family is not found.")
     * )
     */
    public function deleteBlock(Container $container, SequenceFamily $sequenceFamily, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->deleteSequenceFamily($container, $sequenceFamily);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Update sequence family
     * @Route("/rest/container/{containerId}/sequence/family/{familyId}", name="sequence_family_update", methods={"PUT"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("sequenceFamily", expr="repository.find(familyId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param SequenceFamily $sequenceFamily
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Put(
     *     tags={"Sequence Family"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Params: family",
     *          @SWG\Schema(type="string",
     *              example="{""family"": ""Peptides""}"),
     *      ),
     *     @SWG\Response(response="204", description="Sucessfully update sequence family."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container or sequence family is not found.")
     * )
     */
    public function updateBlock(Container $container, SequenceFamily $sequenceFamily, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var FamilyTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new FamilyStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->updateSequenceFamily($trans, $container, $sequenceFamily);
        return ResponseHelper::jsonResponse($modelMessage);
    }

}
