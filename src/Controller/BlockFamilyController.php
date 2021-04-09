<?php

namespace App\Controller;

use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\Constants;
use App\Entity\BlockFamily;
use App\Entity\Container;
use App\Model\ContainerModel;
use App\Repository\BlockFamilyRepository;
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
use OpenApi\Annotations as SWG;

class BlockFamilyController extends AbstractController {

    /**
     * Return block families for logged user
     * @Route("/rest/container/{containerId}/block/family", name="block_family", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @param BlockFamilyRepository $blockFamilyRepository
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Block Family"},
     *     @SWG\Response(response="200", description="Return list of block families in container."),
     *     @SWG\Response(response="403", description="Return when user has not acces to container."),
     *     @SWG\Response(response="404", description="Return when container not found."),
     * )
     */
    public function index(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger, BlockFamilyRepository $blockFamilyRepository) {
        return StaticController::containerGetData($container, $request, $blockFamilyRepository, $this->getDoctrine(), $entityManager, $security->getUser(), $logger, 'findData');
    }

    /**
     * Add new block family for logged user
     * @Route("/rest/container/{containerId}/block/family", name="block_family_new", methods={"POST"})
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
     *     tags={"Block Family"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\RequestBody(
     *          required=true,
     *          description="JSON wit family attribute - name for new family",
     *          @SWG\MediaType(mediaType="application/json",
     *              @SWG\Schema(type="object",
     *                  @SWG\Property(property="family", type="string"),
     *                  example="{""family"": ""Acids""}"),
     *              ),
     *          ),
     *      ),
     *     @SWG\Response(response="201", description="Create new block family."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container is not found.")
     * )
     */
    public function addNewBlockFamily(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var FamilyTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new FamilyStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->createNewBlockFamily($container, $trans);
        return new JsonResponse($modelMessage, $modelMessage->status, isset($modelMessage->id) ? Constants::getLocation('container/' . $container->getId() . '/block/family/', $modelMessage->id) : []);
    }

    /**
     * Delete block family
     * @Route("/rest/container/{containerId}/block/family/{familyId}", name="block_family_delete", methods={"DELETE"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("blockFamily", expr="repository.find(familyId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param BlockFamily $blockFamily
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Delete(
     *     tags={"Block Family"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Sucessfully deleted block family."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container or block family is not found.")
     * )
     */
    public function deleteBlockFamily(Container $container, BlockFamily $blockFamily, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->deleteBlockFamily($container, $blockFamily);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Update block family
     * @Route("/rest/container/{containerId}/block/family/{familyId}", name="block_family_update", methods={"PUT"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("blockFamily", expr="repository.find(familyId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param BlockFamily $blockFamily
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Put(
     *     tags={"Block Family"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\RequestBody(
     *          required=true,
     *          description="Params: family - new name for family",
     *          @SWG\MediaType(mediaType="application/json",
     *              @SWG\Schema(type="object",
     *                  @SWG\Property(property="family", type="string"),
     *                  example="{""family"": ""Acids 2""}"),
     *              ),
     *          ),
     *      ),
     *     @SWG\Response(response="204", description="Sucessfully update block family."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container or block family is not found.")
     * )
     */
    public function updateBlock(Container $container, BlockFamily $blockFamily, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var FamilyTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new FamilyStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->updateBlockFamily($trans, $container, $blockFamily);
        return ResponseHelper::jsonResponse($modelMessage);
    }

}
