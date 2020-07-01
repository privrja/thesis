<?php

namespace App\Controller;

use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Entity\EntityColumnsEnum;
use App\Model\ContainerModel;
use App\Repository\ContainerRepository;
use App\Repository\UserRepository;
use App\Structure\NewContainerStructure;
use App\Structure\NewContainerTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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
     */
    public function index(UserRepository $userRepository, Security $security) {
        // TODO prepare sorting and filtering options to query, maybe paging
        return new JsonResponse($userRepository->findContainersForLoggedUser($security->getUser()->getId()));
    }

    /**
     * Return containers which is free to read
     * @Route("/rest/container/free", name="container_free", methods={"GET"})
     * @param ContainerRepository $containerRepository
     * @return JsonResponse
     */
    public function freeContainers(ContainerRepository $containerRepository) {
        // TODO prepare sorting and filtering options to query, maybe paging
        return new JsonResponse($containerRepository->findBy([EntityColumnsEnum::CONTAINER_VISIBILITY => 1]));
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
     */
    public function addNewContainer(Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var NewContainerTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new NewContainerStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }

        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->newContainer($trans);
        if (!$modelMessage->result) {
            return ResponseHelper::jsonResponse($modelMessage, Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        // TODO specification of REST API
    }

    /**
     * Delete container with all content -> delete all blocks, sequences, modifications, etc.
     * @Route("/rest/container", name="container_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteContainer(Request $request) {
        // TODO
        return new JsonResponse();
    }

    /**
     * Update container values (name, visibility)
     * @Route("/rest/container", name="container_delete", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return JsonResponse
     */
    public function updateContainer(Request $request) {
        // TODO, zajistit nepovinost parametru (staci jeden)
        return new JsonResponse();
    }

}
