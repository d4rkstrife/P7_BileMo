<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('api/product', name: 'app_product', methods: ['GET'])]
    public function index(): Response
    {
        return $this->json([], 201);
    }
}
