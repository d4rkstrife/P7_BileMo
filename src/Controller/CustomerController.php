<?php

namespace App\Controller;

use DateTime;
use App\Entity\Customer;
use App\Service\Paginator;
use Symfony\Component\Uid\Uuid;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class CustomerController extends AbstractController
{
    #[Route('/api/customers/', name: 'customers', methods: ['GET'])]
    public function readAll(Paginator $paginator): Response
    {
        $user = $this->getUser();
        $paginator->createPagination(Customer::class, ['reseller' => $user], ['createdAt' => "desc"], 'app.customerperpage');
        return $this->json($paginator->getDatas(), 201, context: ['groups' => 'customer:read']);
    }

    #[Route('/api/customers', name: 'addCustomer', methods: ['POST'])]
    public function addOne(CustomerRepository $customerRepo, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        if (!$request->getContent()) {
            return new Response('
            Le formulaire doit être présenté comme suit:
            {
                "firstName":"",
                "lastName":"",
                "adress":"",
                "email":""
            }
            ', 400);
        }
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        if ($customer->getAdress() === null || $customer->getFirstName() === null || $customer->getLastName() === null || $customer->getEmail() === null) {
            //dd('ce champ doit etre rempli');
            return new Response('
            Le formulaire doit être présenté comme suit toto:
            {
                "firstName":"",
                "lastName":"",
                "adress":"",
                "email":""
            }
            ', 400);
        }

        $customer->setCreatedAt(new DateTime());
        $customer->setUuid(Uuid::v4());
        $customer->setReseller($this->getUser());
        $exceptions = $validator->validate($customer);

        if (count($exceptions) !== 0) {
            $violations = [];
            foreach ($exceptions as $violation) {
                $violations[] = $violation->getMessage();
            }
            return $this->json($violations, 422);
        }
        $customerRepo->add($customer);

        return $this->json($customer, 201, context: ['groups' => 'customer:read']);
    }

    #[Route('/api/customers/{uuid}', name: 'customer_details', methods: ['GET'])]
    public function customerDetails(Uuid $uuid): Response
    {
        dd($uuid);
        return $this->json($customer, 201, context: ['groups' => 'customer:read']);
    }

    #[Route('/api/customers/{uuid}', name: 'customerModification', methods: ['PUT'])]
    public function customerModification(Customer $customer, EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        //dd($customer);
        if (!$request->getContent()) {
            return new Response('
            Le formulaire doit être présenté comme suit:
            {
                "firstName":"",
                "lastName":"",
                "adress":"",
                "email":""
            }
            ', 400);
        }
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $customer]);

        $exceptions = $validator->validate($customer);

        if (count($exceptions) !== 0) {
            $violations = [];
            foreach ($exceptions as $violation) {
                $violations[] = $violation->getMessage();
            }
            return $this->json($violations, 422);
        }

        $entityManager->flush();

        return $this->json($customer, 200, context: ['groups' => 'customer:read']);
    }
}
