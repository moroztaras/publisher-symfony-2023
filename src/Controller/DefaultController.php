<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #Atribute from PHP 8
    #[Route('index')]
    public function index():Response
    {
        return $this->json(['test' => 'index']);
    }
}
