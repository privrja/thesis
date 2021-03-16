<?php

namespace App\Controller;

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
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Setup similarity method computing for application, values: name or tanimoto",
     *          @SWG\Schema(type="string",
     *              example="{""name"":""kokoska"",""password"":""H6saf@sd%sdp""}")
     *      ),
     *     @SWG\Response(response="201", description="User created"),
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
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Setup similarity method computing for application, values: name or tanimoto",
     *          @SWG\Schema(type="string",
     *              example="{""password"":""H6saf@sd%sdp2""}")
     *      ),
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
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Apikey for ChemSpider, you need to obtain this on your Chemspider account",
     *          @SWG\Schema(type="string",
     *              example="{""apiKey"":""YyFGYKE4rVH886ywQs8kwKDEBeBo1fAO""}")
     *      ),
     *     @SWG\Response(response="204", description="Add new Chemspider apikey."),
     *     @SWG\Response(response="401", description="Return when user is not logged in.")
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
     *
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

}
