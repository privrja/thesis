<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SequenceController extends AbstractController
{
    /**
     * @Route("/sequence", name="sequence")
     */
    public function index()
    {
        return $this->render('sequence/index.html.twig', [
            'controller_name' => 'SequenceController',
        ]);
    }
}
