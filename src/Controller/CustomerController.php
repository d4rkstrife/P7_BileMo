<?php

namespace App\Controller;

use DateTime;
use App\Entity\Customer;
use App\Service\Paginator;
use Symfony\Component\Uid\Uuid;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomerController extends AbstractController
{
    #[Route('/api/customers/', name: 'customers', methods: ['GET'])]
    public function readAll(Paginator $paginator): Response
    {
        $user = $this->getUser();
        $paginator->createPagination(Customer::class, ['reseller' => $user], ['createdAt' => "desc"], 'app.customerperpage');
        return $this->json($paginator->getDatas(), 201, context: ['groups' => 'customer:read']);
    }

    #[Route('/api/customers/', name: 'addCustomer', methods: ['POST'])]
    public function addOne(CustomerRepository $customerRepo, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $customer->setCreatedAt(new DateTime());
        $customer->setUuid(Uuid::v4());
        $customer->setReseller($this->getUser());
        $exceptions = $validator->validate($customer);
        dd($exceptions);
        $customerRepo->add($customer);

        return $this->json('ok');
    }

    #[Route('/api/customers/{uuid}', name: 'customer_details', methods: ['GET'])]
    public function productDetails(Customer $customer): Response
    {
        return $this->json($customer, 201, context: ['groups' => 'customer:read']);
    }
}
