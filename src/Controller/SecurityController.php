<?php

namespace App\Controller;

use App\Base\Cap;
use App\Base\GeneratorHelper;
use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\ErrorConstants;
use App\Entity\U2c;
use App\Entity\User;
use App\Enum\ContainerModeEnum;
use App\Repository\ContainerRepository;
use App\Repository\U2cRepository;
use App\Repository\UserRepository;
use App\Structure\ChemSpiderKeyExport;
use App\Structure\ChemSpiderKeyStructure;
use App\Structure\ChemSpiderKeyTransformed;
use App\Structure\GenerateStructure;
use App\Structure\GenerateTransformed;
use App\Structure\MailStructure;
use App\Structure\MailTransformed;
use App\Structure\NewRegistrationStructure;
use App\Structure\NewRegistrationTransformed;
use App\Structure\PassStructure;
use App\Structure\PassTransformed;
use App\Structure\ResetStructure;
use App\Structure\ResetTransformed;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use OpenApi\Annotations as SWG;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController {

    const QUESTION = 'question';
    private $session;

    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }

    const REG_TOKEN = 'x-reg-token';

    /**
     * Registration pre-request with captcha
     * @Route("/rest/cap", name="cap", methods={"GET"})
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Auth"},
     *     @SWG\Response(response="200", description="Return registration token and cap question")
     * )
     */
    public function cap() {
        $question = Cap::getRandomAcid();
        $this->session->set(self::QUESTION, $question);
        return new JsonResponse([self::QUESTION => $question]);
    }

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
     *     @SWG\RequestBody(
     *          required=true,
     *          description="Create new user",
     *          @SWG\MediaType(mediaType="application/json",
     *              @SWG\Schema(type="string",
     *                  @SWG\Property(property="name", type="string"),
     *                  @SWG\Property(property="password", type="string"),
     *                  example="{""name"":""kokoska"",""password"":""H6saf@sd%sdp""}")
     *              ),
     *          ),
     *      ),
     * @SWG\Response(response="201", description="User created"),
     * @SWG\Response(response="400", description="Name is taken")
     * )
     */
    public function registration(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository, LoggerInterface $logger) {
        /** @var NewRegistrationTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new NewRegistrationStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        } else if ($userRepository->findOneBy(['nick' => $trans->getName()])) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_NAME_IS_TAKEN));
        } else if (isset($trans->mail) && $userRepository->findOneBy(['mail' => $trans->mail])) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_MAIL_IS_TAKEN));
        }

        $question = $this->session->get(self::QUESTION);
        if ($question === null) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::QUESTION_EMPTY));
        }
        if (!Cap::verify($question, $trans->cap)) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::CAP_VERIFY_FAILURE));
        }

        $user = new User();
        $user->setNick($trans->getName());
        $user->setConditions(true);
        $user->setRoles(["ROLE_USER"]);
        if (!empty($trans->getMail())) {
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
     * Get list of users
     * @Route("/rest/user", name="user", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param UserRepository $userRepository
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Auth"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
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
     *
     * @SWG\Post(
     *     tags={"Auth"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Conditions agreed"),
     *     @SWG\Response(response="401", description="Bad auth")
     * )
     */
    public function conditions(Security $security, EntityManagerInterface $entityManager) {
        $user = $security->getUser();
        $user->setConditions(true);
        $entityManager->persist($user);
        $entityManager->flush();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Change password
     * @Route("/rest/user", name="change", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Put(
     *     tags={"Auth"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\RequestBody(
     *          required=true,
     *          description="Change password",
     *          @SWG\MediaType(mediaType="application/json",
     *              @SWG\Schema(type="object",
     *                  @SWG\Property(property="password", type="string"),
     *                  example="{""password"":""H6saf@sd%sdp2""}")
     *              ),
     *          ),
     *      ),
     * @SWG\Response(response="201", description="Password changed"),
     * @SWG\Response(response="500", description="Internal server Error"),
     * @SWG\Response(response="400", description="Name is taken")
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

    /**
     * Remove Email
     * @Route("/rest/user/mail", name="mail_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Auth"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Removed"),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     * )
     */
    public function removeMail(Security $security, EntityManagerInterface $entityManager) {
        /** @var User $user */
        $user = $security->getUser();
        $user->setMail(null);
        $entityManager->persist($user);
        $entityManager->flush();
        return ResponseHelper::jsonResponse(Message::createNoContent());
    }

    /**
     * Get Email
     * @Route("/rest/user/mail", name="mail", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param Security $security
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Auth"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="200", description="Mail"),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Unauthorized")
     * )
     *
     */
    public function getMail(Security $security) {
        /** @var User $user */
        $user = $security->getUser();
        return new JsonResponse(['mail' => $user->getMail()]);
    }

    /**
     * Change mail
     * @Route("/rest/user/mail", name="change_mail", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return MailTransformed|JsonResponse
     *
     * @SWG\Put(
     *     tags={"Auth"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\RequestBody(
     *          required=true,
     *          description="Set new value for email",
     *          @SWG\MediaType(mediaType="application/json",
     *              @SWG\Schema(type="object",
     *                  @SWG\Property(property="mail", type="string"),
     *                  example="{""mail"":""kokos@xx.cz""}")
     *              ),
     *          ),
     *      ),
     * @SWG\Response(response="201", description="Mail changed"),
     * @SWG\Response(response="400", description="Name is taken")
     * )
     *
     */
    public function changeMail(Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var MailTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new MailStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        /** @var User $user */
        $user = $security->getUser();
        $user->setMail($trans->mail);
        $entityManager->persist($user);
        $entityManager->flush();
        return ResponseHelper::jsonResponse(Message::createNoContent());
    }

    /**
     * Remove ChemSpider key
     * @Route("/rest/chemspider/key", name="chemspider_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     *
     * @SWG\Get(
     *  tags={"Setup"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Return Done."),
     *     @SWG\Response(response="401", description="Return when user is not logged in.")
     * )
     */
    public function deleteChemSpiderKey(Security $security, EntityManagerInterface $entityManager) {
        /** @var User $user */
        $user = $security->getUser();
        $user->setChemSpiderToken(null);
        $entityManager->persist($user);
        $entityManager->flush();
        return ResponseHelper::jsonResponse(Message::createNoContent());
    }

    /**
     * Get ChemSpider key from database
     * @Route("/rest/chemspider/key", name="chemspider_get_key", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param Security $security
     * @return JsonResponse
     *
     * @SWG\Get(
     *  tags={"Setup"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="200", description="Return Chemspider apikey."),
     *     @SWG\Response(response="401", description="Return when user is not logged in.")
     * )
     */
    public function chemSpiderKey(Security $security) {
        /** @var User $user */
        $user = $security->getUser();
        $export = new ChemSpiderKeyExport();
        $export->apiKey = $user->getChemSpiderToken();
        return new JsonResponse($export);
    }

    /**
     * Set ChemSpider key from database
     * @Route("/rest/chemspider/key", name="chemspider_create_key", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *  tags={"Setup"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\RequestBody(
     *          required=true,
     *          description="Apikey for ChemSpider, you need to obtain this on your Chemspider account",
     *          @SWG\MediaType(mediaType="application/json",
     *              @SWG\Schema(type="object",
     *                  @SWG\Property(property="apiKey", type="string"),
     *                  example="{""apiKey"":""YyFGYKE4rVH886ywQs8kwKDEBeBo1fAO""}")
     *              ),
     *          ),
     *      ),
     * @SWG\Response(response="204", description="Add new Chemspider apikey."),
     * @SWG\Response(response="401", description="Return when user is not logged in.")
     * )
     */
    public function createChemSpiderKey(Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var ChemSpiderKeyTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new ChemSpiderKeyStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        /** @var User $user */
        $user = $security->getUser();
        $user->setChemSpiderToken($trans->apiKey);
        $entityManager->persist($user);
        $entityManager->flush();
        return ResponseHelper::jsonResponse(Message::createNoContent());
    }

    /**
     * Delete user
     * @Route("/rest/user", name="user_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     * @param U2cRepository $u2cRepository
     * @param ContainerRepository $containerRepository
     * @param UserRepository $userRepository
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     *
     * @SWG\Delete(
     *  tags={"Auth"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="User deleted."),
     *     @SWG\Response(response="401", description="Bad auth."),
     * )
     */
    public function deleteUser(U2cRepository $u2cRepository, ContainerRepository $containerRepository, UserRepository $userRepository, Security $security, EntityManagerInterface $entityManager) {
        /** @var User $usr */
        $usr = $security->getUser();
        if ($usr->getNick() === 'admin') {
            return ResponseHelper::jsonResponse(new Message('Admin can\'t be deleted'));
        }
        $entityManager->beginTransaction();
        $containers = $u2cRepository->userDeleteContainers($usr->getId());
        foreach ($containers as $container) {
            $cont = $containerRepository->find($container['container_id']);
            switch ($container['operation_todo']) {
                case 'DELETE':
                    $entityManager->remove($cont);
                    $entityManager->flush();
                    break;
                case 'UPGRADE':
                    foreach ($cont->getC2users() as $role) {
                        if ($role->getMode() === ContainerModeEnum::RW) {
                            $role->setMode(ContainerModeEnum::RWM);
                            $entityManager->persist($role);
                            $entityManager->flush();
                        }
                    }
                    break;
                case 'ADMIN':
                    foreach ($cont->getC2users() as $role) {
                        if ($role->getUser()->getNick() == 'admin') {
                            $entityManager->remove($role);
                            $entityManager->flush();
                        }
                    }
                    $u2c = new U2c();
                    $u2c->setContainer($cont);
                    $u2c->setMode(ContainerModeEnum::RWM);
                    $u2c->setUser($userRepository->findOneBy(['nick' => 'admin']));
                    $entityManager->persist($u2c);
                    $entityManager->flush();
                    break;
                default:
                    $entityManager->rollback();
                    return ResponseHelper::jsonResponse(new Message('Something goes wrong'));
            }
        }
        $entityManager->remove($usr);
        $entityManager->flush();
        $entityManager->commit();
        return ResponseHelper::jsonResponse(Message::createNoContent());
    }

    /**
     * Reset
     * @Route("/rest/user/reset", name="reset", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Auth"},
     *     @SWG\Response(response="200", description="Mail send"),
     *     @SWG\Response(response="500", description="Internal server Error"),
     *     @SWG\Response(response="400", description="Wrong input")
     * )
     *
     */
    public function reset(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, LoggerInterface $logger) {
        /** @var ResetTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new ResetStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $user = $userRepository->findOneBy(['mail' => $trans->mail]);
        if (!isset($user)) {
            return ResponseHelper::jsonResponse(new Message("User not found"));
        }
        try {
            $user->setApiToken(GeneratorHelper::generate(32));
            $user->setLastActivity(new DateTime());
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (Exception $exception) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_SOMETHING_GO_WRONG, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
        $email = $user->getMail();
        if (!empty($email)) {
            try {
                mail($user->getMail(), 'MassSpecBlocks - password reset', 'You requested a new password for MassSpecBlocks. We have generated a unique code to verify that it\'s you: ' . $user->getApiToken()  . '. Please, copy and paste this code into the password reset dialog. After that, we will generate a new password and send it via email. After the first login with the new password, we recommend you change it. Thanks');
            } catch (Exception $exception) {
                return ResponseHelper::jsonResponse(new Message('Server doesn\'t support sending mails'));
            }
        } else {
            return ResponseHelper::jsonResponse(new Message('Mail not set for this user'));
        }
        return ResponseHelper::jsonResponse(Message::createNoContent());
    }

    /**
     * Generate new password
     * @Route("/rest/user/generate", name="generate", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Auth"},
     *     @SWG\Response(response="200", description="Mail send"),
     *     @SWG\Response(response="500", description="Internal server Error"),
     *     @SWG\Response(response="400", description="Wrong input")
     * )
     *
     */
    public function generatePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository, LoggerInterface $logger) {
        /** @var GenerateTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new GenerateStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $usr = $userRepository->findByMailToken($trans->mail, $trans->token);
        if (!empty($usr)) {
            $user = $userRepository->findOneBy(['id' => $usr[0]['id']]);
            $pass = null;
            try {
                $pass = bin2hex(random_bytes(8));
            } catch (Exception $e) {
                $pass = rand(1000000, 999999999999);
            }
            try {
                $user->setPassword($passwordEncoder->encodePassword($user, $pass));
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (Exception $exception) {
                return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_SOMETHING_GO_WRONG, Response::HTTP_INTERNAL_SERVER_ERROR));
            }
            try {
                mail($user->getMail(), 'MassSpecBlocks - password reset', 'You request a new password for Mass Spec Blocks. New generated password: ' . $pass . '. After first login with new password we recommended you to change it. Thanks');
            } catch (Exception $exception) {
                return ResponseHelper::jsonResponse(new Message('Server doesn\'t support sending mails'));
            }
            /** On purpose to replace stored password in memory */
            $pass = '12345678';
            return ResponseHelper::jsonResponse(Message::createNoContent());
        } else {
            return ResponseHelper::jsonResponse(new Message('Bad verify'));
        }
    }

}
