<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DbTestControlleurController extends AbstractController
{
    #[Route('/db/test/controlleur', name: 'app_db_test_controlleur')]
    public function index(): Response
    {
        return $this->render('db_test_controlleur/index.html.twig', [
            'controller_name' => 'DbTestControlleurController',
        ]);
    }
}
