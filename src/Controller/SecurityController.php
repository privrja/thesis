<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\ResponseHelper;
use App\Constant\ErrorConstants;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use JsonMapper;
use JsonMapper_Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController {

    /**
     * @Route("/rest/register", name="register", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function registration(Request $request, EntityManagerInterface $entityManager, Security $security, UserPasswordEncoderInterface $passwordEncoder, LoggerInterface $logger) {
        if($security->getUser()) {
            return ResponseHelper::jsonResponse(new Message("Already registered user"), Response::HTTP_BAD_REQUEST);
        }

        $mapper = new JsonMapper();
        // TODO check if user name exists

        $user = new User();
        try {
            /** @var NewRegistrationStructure $regData */
            $regData = $mapper->map(json_decode($request->getContent()), new NewRegistrationStructure());
            $user->setNick($regData->getName());
            if ($regData->getMail() !== null) {
                $user->setMail($regData->getMail());
            }
            $user->setPassword($passwordEncoder->encodePassword($user, $regData->getPassword()));

            $entityManager->persist($user);
            $entityManager->flush();
        } catch (JsonMapper_Exception | InvalidArgumentException  $e) {
            $logger->warning($e->getMessage(), $e->getTrace());
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_JSON_FORMAT), Response::HTTP_BAD_REQUEST);
        }
        return ResponseHelper::jsonResponse(Message::createOkMessage(), Response::HTTP_CREATED);
    }

}
