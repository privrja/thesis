<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SequenceController extends AbstractController
{
    /**
     * @Route("/sequence", name="sequence")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        return $this->render('sequence/index.html.twig', [
            'controller_name' => 'SequenceController',
        ]);
    }
}
