<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DbTestController extends AbstractController
{
    #[Route('/db/test/controller', name: 'app_db_test_controller')]
    public function index(): Response
    {
        echo "Hello Biblio";
        exit();
    }
}
