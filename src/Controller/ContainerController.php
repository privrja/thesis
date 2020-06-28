<?php

namespace App\Controller;

use App\Entity\EntityColumnsEnum;
use App\Repository\ContainerRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ContainerController extends AbstractController {
    /**
     * @Route("/rest/container", name="container", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param UserRepository $userRepository
     * @param Security $security
     * @return JsonResponse
     */
    public function index(UserRepository $userRepository, Security $security) {
        return new JsonResponse($userRepository->findContainersForLoggedUser($security->getUser()->getId()));
    }

    /**
     * @Route("/rest/container/free", name="container_free", methods={"GET"})
     * @param ContainerRepository $containerRepository
     * @return JsonResponse
     */
    public function freeContainers(ContainerRepository $containerRepository) {
        return new JsonResponse($containerRepository->findBy([EntityColumnsEnum::CONTAINER_VISIBILITY => 1]));
    }

}
