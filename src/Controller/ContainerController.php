<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ContainerController extends AbstractController
{
    /**
     * @Route("/container", name="container")
     */
    public function index()
    {
        return $this->render('container/index.html.twig', [
            'controller_name' => 'ContainerController',
        ]);
    }
}
