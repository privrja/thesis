<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ModificationController extends AbstractController
{
    /**
     * @Route("/modification", name="modification")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        return $this->render('modification/index.html.twig', [
            'controller_name' => 'ModificationController',
        ]);
    }
}
