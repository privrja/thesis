<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator {
    const AUTHENTICATION_REQUIRED = 'Authentication Required';
    const WRONG_CREDENTIALS = "Wrong credentials";
    const X_AUTH_TOKEN = 'X-AUTH-TOKEN';
    const X_CONDITION = 'X-CONDITION';

    private $em;
    private $passwordEncoder;
    private $logger;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, LoggerInterface $logger) {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->logger = $logger;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request) {
        return $request->headers->has(self::X_AUTH_TOKEN);
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     * @param Request $request
     * @return array
     */
    public function getCredentials(Request $request) {
        $token = $request->headers->get(self::X_AUTH_TOKEN);
        $type = true;
        $username = $secret = null;

        if (false === strpos($token, ':')) {
            $type = false;
        } else {
            list($username, $secret) = explode(':', $token, 2);
        }

        return [
            'type' => $type,
            'token' => $request->headers->get(self::X_AUTH_TOKEN),
            'nick' => $username,
            'secret' => $secret
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        if ($credentials['type'] === true) {
            return $userProvider->loadUserByUsername($credentials['nick']);
        } else {
            return $this->em->getRepository(User::class)->findOneBy(['apiToken' => $credentials['token']]);
        }
    }

    public function checkCredentials($credentials, UserInterface $user) {
        if ($credentials['type']) {
            return $this->passwordEncoder->isPasswordValid($user, $credentials['secret']);
        }
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        $credentials = $this->getCredentials($request);
        if ($credentials['type'] === true) {
            $genToken = $this->generateToken();
            /** @var User $user */
            $user = $token->getUser()->setApiToken($genToken);
            $this->em->beginTransaction();
            $this->em->persist($user);
            $this->em->commit();
            $this->em->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT, [self::X_AUTH_TOKEN => $genToken, self::X_CONDITION => $user->getConditions()]);
        }
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $this->logger->warning(strtr($exception->getMessageKey(), $exception->getMessageData()));
        return new JsonResponse(['message' => self::WRONG_CREDENTIALS], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null) {
        return new JsonResponse(['message' => self::AUTHENTICATION_REQUIRED], Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe() {
        return false;
    }

    public function generateToken() {
        try {
            return bin2hex(random_bytes(64));
        } catch (Exception $e) {
            $this->logger->warning($e);
            return rand(1000000, 999999999999);
        }
    }

}
