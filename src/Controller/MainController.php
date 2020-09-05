<?php

namespace App\Controller;

use App\Base\ResponseHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
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
     *
     */
    public function rest()
    {
        return ResponseHelper::jsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/", name="main", methods={"GET"})
     */
    public function main()
    {
        return new RedirectResponse('/api/doc');
    }

}
