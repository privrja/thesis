<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\Constants;
use App\Constant\ErrorConstants;
use App\Entity\Container;
use App\Entity\Modification;
use App\Enum\ContainerVisibilityEnum;
use App\Model\ContainerModel;
use App\Repository\ModificationRepository;
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
use OpenApi\Annotations as SWG;

class ModificationController extends AbstractController {

    /**
     * Return modifications for container
     * @Route("/rest/container/{containerId}/modification", name="modification", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Modification"},
     *     description="Get list of modifications. When you want to sort it you should add at the end of URL ?sort=modificationName&order=asc, where sort can be id, modificationName,  modificationFormula, modificationMass, nTerminal, cTerminal, organism and order is asc or desc. When you would like to filter data you can add something like modificationName=Ac, params are very similar to sorting only difference is in modificationMassFrom and modificationMassTo which is range.",
     *     @SWG\Response(response="200", description="Return list of containers for logged user."),
     *     @SWG\Response(response="403", description="Return when permisions is insuficient."),
     *     @SWG\Response(response="404", description="Return when container is not found."),
     * )
     *
     */
    public function getModifications(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger, ModificationRepository $modificationRepository) {
        $possibleFilters = ['id', 'modificationName', 'modificationFormula', 'modificationMassFrom', 'modificationMassTo', 'nTerminal', 'cTerminal'];
        $filters = RequestHelper::getFiltering($request, $possibleFilters);
        $filters = RequestHelper::transformFilters($filters, ['nTerminal', 'cTerminal'], ["yes" => 1, "no" => 0]);
        $sort = RequestHelper::getSorting($request);
        if ($security->isGranted('ROLE_USER')) {
            $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
            if ($container->getVisibility() === ContainerVisibilityEnum::PRIVATE) {
                $modelMessage = $model->concreteContainer($container);
                if (!$modelMessage->result) {
                    return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_NOT_FOUND));
                }
            }
            return new JsonResponse($modificationRepository->filters($container, $filters, $sort));
        } else {
            if ($container->getVisibility() === ContainerVisibilityEnum::PRIVATE) {
                return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_NOT_FOUND));
            }
            return new JsonResponse($modificationRepository->filters($container, $filters, $sort));
        }
    }

    /**
     * Delete modification
     * @Route("/rest/container/{containerId}/modification/{modificationId}", name="modification_delete", methods={"DELETE"}, requirements={"modificationId"="\d+"})
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
     *     @SWG\Response(response="403", description="Return when permisions is insuficient."),
     *     @SWG\Response(response="404", description="Return when container is not found."),
     * )
     */
    public function deleteModification(Container $container, Modification $modification, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->deleteModification($container, $modification);
        return ResponseHelper::jsonResponse($modelMessage);
    }

    /**
     * Update modification values
     * @Route("/rest/container/{containerId}/modification/{modificationId}", name="modification_update", methods={"PUT"}, requirements={"modificatinoId"="\d+"})
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
     *     @SWG\RequestBody(
     *          required=true,
     *          description="Paramas: modificationName, formula, nTerminal, cTerminal.",
     *          @SWG\MediaType(mediaType="application/json",
     *              @SWG\Schema(type="object",
     *                  @SWG\Property(property="modificationName", type="string"),
     *                  @SWG\Property(property="formula", type="string"),
     *                  @SWG\Property(property="nTerminal", type="boolean"),
     *                  @SWG\Property(property="cTerminal", type="boolean"),
     *                  example="{""modificationName"":""Modif"",""formula"":""CH2O"",""nTerminal"":false,""cTerminal"":true}"),
     *              ),
     *          ),
     *      ),
     *     @SWG\Response(response="204", description="Sucessfully update container."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insuficient."),
     *     @SWG\Response(response="404", description="Return when container or modification is not found.")
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
     *     @SWG\RequestBody(
     *          required=true,
     *          description="Paramas: modificationName, formula, nTerminal, cTerminal.",
     *          @SWG\MediaType(mediaType="application/json",
     *              @SWG\Schema(type="object",
     *                  @SWG\Property(property="modificationName", type="string"),
     *                  @SWG\Property(property="formula", type="string"),
     *                  @SWG\Property(property="nTerminal", type="boolean"),
     *                  @SWG\Property(property="cTerminal", type="boolean"),
     *                  example="{""modificationName"":""Modif"",""formula"":""CH2O"",""nTerminal"":false,""cTerminal"":true}"),
     *              ),
     *          ),
     *      ),
     *     @SWG\Response(response="201", description="Create new container."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insuficient.")
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
        return new JsonResponse($modelMessage, $modelMessage->status, isset($modelMessage->id) ? Constants::getLocation('container/' . $container->getId() . '/modification/', $modelMessage->id) : []);
    }

}
