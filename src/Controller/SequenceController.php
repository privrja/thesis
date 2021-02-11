<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\EntityColumnsEnum;
use App\Constant\ErrorConstants;
use App\Entity\Container;
use App\Enum\ContainerVisibilityEnum;
use App\Model\ContainerModel;
use App\Repository\SequenceRepository;
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
     * @Route("/rest/container/{containerId}/sequence", name="block", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
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
     *     @SWG\Response(response="404", description="Return when container not found."),
     * )
     */
    public function index(Container $container, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger, SequenceRepository $sequenceRepository) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC) {
            return new JsonResponse($sequenceRepository->findBy([EntityColumnsEnum::CONTAINER => $container->getId()]), Response::HTTP_OK);
        } else {
            if ($security->getUser() !== null) {
                $containerModel = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
                if ($containerModel->hasContainer($container->getId())) {
                    return new JsonResponse($sequenceRepository->findBy([EntityColumnsEnum::CONTAINER => $container->getId()]), Response::HTTP_OK);
                } else {
                    return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_NOT_FOUND));
                }
            } else {
                return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_UNAUTHORIZED));
            }
        }
    }

}
