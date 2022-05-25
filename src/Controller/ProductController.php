<?php

namespace App\Controller;

use App\Entity\Phone;
use Symfony\Component\Uid\Uuid;
use App\Repository\PhoneRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    public function __construct(PhoneRepository $phoneRepo)
    {
        $this->phoneRepo = $phoneRepo;
    }

    #[Route('api/products', name: 'app_product', methods: ['GET'])]
    public function allProducts(): Response
    {
        $phones = $this->phoneRepo->findAll();

        return $this->json($phones, 201);
    }

    #[Route('/api/products/{uuid}', name: 'product_details', methods: ['GET'])]
    public function productDetails(Phone $phone): Response
    {
        return $this->json($phone, 201);
    }
}
