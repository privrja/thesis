<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\ErrorConstants;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Structure\NewRegistrationStructure;
use App\Structure\NewRegistrationTransformed;
use App\Structure\PassStructure;
use App\Structure\PassTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController {

    /**
     * Registration of new user
     * @Route("/rest/register", name="register", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Auth"},
     *     @SWG\Response(response="201", description="User created"),
     *     @SWG\Response(response="500", description="Internal server Error"),
     *     @SWG\Response(response="400", description="Name is taken")
     * )
     */
    public function registration(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository, LoggerInterface $logger) {
        /** @var NewRegistrationTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new NewRegistrationStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        } else if ($userRepository->findOneBy(['nick' => $trans->getName()])) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_NAME_IS_TAKEN));
        }
        $user = new User();
        $user->setNick($trans->getName());
        $user->setConditions(true);
        $user->setRoles(["ROLE_USER"]);
        if ($trans->getMail() !== null) {
            $user->setMail($trans->getMail());
        }
        try {
            $user->setPassword($passwordEncoder->encodePassword($user, $trans->getPassword()));
            $trans->setPassword('');
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (Exception $exception) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_SOMETHING_GO_WRONG, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
        return ResponseHelper::jsonResponse(Message::createCreated());
    }

    /**
     * Registration of new user
     * @Route("/rest/user", name="user", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param UserRepository $userRepository
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Auth"},
     *     @SWG\Response(response="200", description="List od users"),
     *     @SWG\Response(response="401", description="Bad auth")
     * )
     */
    public function index(UserRepository $userRepository) {
        return new JsonResponse($userRepository->findAll());
    }

    /**
     * Agree with conditions
     * @Route("/rest/condition", name="user_condition", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function conditions(Security $security, EntityManagerInterface $entityManager) {
        $user = $security->getUser();
        $user->setConditions(true);
        $entityManager->persist($user);
        $entityManager->flush();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Change
     * @Route("/rest/user", name="change", methods={"PUT"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Auth"},
     *     @SWG\Response(response="201", description="Password changed"),
     *     @SWG\Response(response="500", description="Internal server Error"),
     *     @SWG\Response(response="400", description="Name is taken")
     * )
     */
    public function change(Request $request, EntityManagerInterface $entityManager, Security $security, UserPasswordEncoderInterface $passwordEncoder, LoggerInterface $logger) {
        /** @var PassTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new PassStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        /** @var User $user */
        $user = $security->getUser();
        try {
            $user->setPassword($passwordEncoder->encodePassword($user, $trans->getPassword()));
            $trans->setPassword('');
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (Exception $exception) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_SOMETHING_GO_WRONG, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
        return ResponseHelper::jsonResponse(Message::createNoContent());
    }


}
