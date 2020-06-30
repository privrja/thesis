<?php

namespace App\Controller;

use App\Constant\ContainerModeEnum;
use App\Entity\Container;
use App\Entity\EntityColumnsEnum;
use App\Entity\U2c;
use App\Repository\ContainerRepository;
use App\Repository\UserRepository;
use App\Structure\AbstractStructure;
use App\Structure\NewContainerStructure;
use App\Structure\NewContainerTransformed;
use Doctrine\ORM\EntityManagerInterface;
use JsonMapper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * Add new container for logged user
     * @Route("/rest/container", name="container_new", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @return JsonResponse
     */
    public function addNewContainer(Request $request, EntityManagerInterface $entityManager, Security $security) {
        $data = json_decode($request->getContent());
        $mapper = new JsonMapper();

        /** @var AbstractStructure $newContainerData */
        $newContainerData = $mapper->map($data, new NewContainerStructure());
        $message = $newContainerData->checkInput();
        if(!$message->result) {
            return new JsonResponse(json_encode($message->messageText), Response::HTTP_BAD_REQUEST);
        }
        /** @var NewContainerTransformed $trans */
        $trans = $newContainerData->transform();

        // TODO check for controllers with same name for user?

        $usr = $security->getUser();
        $container = new Container();
        $container->setName($trans->getName());
        $container->setVisibility($trans->getVisibility());
        $entityManager->persist($container);

        $u2c = new U2c();
        $u2c->setUser($usr);
        $u2c->setContainer($container);
        $u2c->setMode(ContainerModeEnum::RW);
        $entityManager->persist($u2c);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        // TODO test it
    }

}
