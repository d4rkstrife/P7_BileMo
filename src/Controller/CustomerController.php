<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomerController extends AbstractController
{
    #[Route('/api/customers/', name: 'customers', methods: ['GET'])]
    public function readAll(CustomerRepository $customerRepo): Response
    {
        $user = $this->getUser();
        $customers = $customerRepo->findBy(['reseller' => $user]);
        return $this->json($customers, 201, context: ['groups' => 'customer:read']);
    }

    #[Route('/api/customers/{uuid}', name: 'customer_details', methods: ['GET'])]
    public function productDetails(Customer $customer): Response
    {
        return $this->json($customer, 201, context: ['groups' => 'customer:details']);
    }
}
