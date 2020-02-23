<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ModificationController extends AbstractController
{
    /**
     * @Route("/modification", name="modification")
     */
    public function index()
    {
        return $this->render('modification/index.html.twig', [
            'controller_name' => 'ModificationController',
        ]);
    }
}
