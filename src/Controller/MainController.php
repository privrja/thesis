<?php

namespace App\Controller;

use App\Base\ResponseHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/rest", name="rest", methods={"GET"})
     * @IsGranted("ROLE_USER")
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
