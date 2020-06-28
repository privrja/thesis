<?php

namespace App\Controller;

use App\Repository\ContainerRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ContainerController extends AbstractController
{
    /**
     * @Route("/rest/container", name="container", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function index(UserRepository $userRepository) {
//        $auth_checker = $this->get('security.authorization_checker');
//        $isAuth = $auth_checker->isGranted('ROLE_USER') || $auth_checker->isGranted('ROLE_ADMIN');
//        if ($isAuth) {
            return new JsonResponse($userRepository->findContainersForLoggedUser());
    }

    /**
     * @Route("/rest/container/free", name="container_free", methods={"GET"})
     * @param UserRepository $userRepository
     * @param ContainerRepository $containerRepository
     * @return JsonResponse
     */
    public function freeContainers(ContainerRepository $containerRepository) {
        return new JsonResponse($containerRepository->findBy(['visibility' => 0]));
    }

}
