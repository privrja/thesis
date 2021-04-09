<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as SWG;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController {

    /**
     * Auth
     * @Route("/rest", name="rest", methods={"GET"})
     * @IsGranted("ROLE_USER")
     *
     * @SWG\Get(
     *     tags={"Auth"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     description="For login use header X-AUTH-TOKEN for first time with value: 'username:password', as a response you get API token. After that you send as a value this generated token.",
     *     @SWG\Response(response="200", description="Return when user is logged in."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     * )
     * @param Security $security
     * @return JsonResponse
     */
    public function rest(Security $security) {
        /** @var User $user */
        $user = $security->getUser();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT, ['x-condition' => $user->getConditions()]);
    }

    /**
     * @Route("/", name="main", methods={"GET"})
     */
    public function main() {
        return new RedirectResponse('/api/doc');
    }

}
