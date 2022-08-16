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
    #[Route('/api/customers/', name: 'app_customers', methods: ['GET'])]
    public function readAll(Paginator $paginator): Response
    {
        $user = $this->getUser();
        $paginator->createPagination(Customer::class, ['reseller' => $user], ['createdAt' => "desc"], 'app.customerperpage');
        if($paginator->getDatas() === null){
            return $this->json('Aucun client trouvé', 404);
        }
        //return $this->json($paginator, 200, context: ['groups' => 'customer:read']);
        return $this->json($paginator, 200, context: [
            'callbacks'=>['reseller'=> function ($reseller){
                return $reseller->getCompany();
            }],
            ['route' => 'app_customers']
        ]);
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
    public function customerDetails(Uuid $uuid, CustomerRepository $customerRepo): Response
    {   
        //vérification utilisateur (uuid, 404 not found)     
        $customer = $customerRepo->findOneBy(['uuid'=>$uuid, 'reseller'=>$this->getUser()]);
        if (!$customer){
            //doit retouner json
            return $this->json(["Uuid"=>"Not found"], 404);
        }
        return $this->json($customer, 200, context: ['groups' => 'customer:read', 'type' => 'details']);
    }

    #[Route('/api/customers/{uuid}', name: 'customerModification', methods: ['PUT'])]
    public function customerModification(Uuid $uuid,CustomerRepository $customerRepo, EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $oldCustomer = $customerRepo->findOneBy(['uuid'=>$uuid, 'reseller'=>$this->getUser()]);

        if (!$oldCustomer){
            //doit retouner json
            return $this->json(["Uuid"=>"Not found"], 404);
        }

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
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $oldCustomer]);
        
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

    #[Route('/api/customers/{uuid}', name: 'customerDelete', methods: ['DELETE'])]
    public function customerDelete(Uuid $uuid, CustomerRepository $customerRepository, EntityManagerInterface $em): Response
    {
        $customer = $customerRepository->findOneBy(['uuid'=> $uuid,  'reseller'=>$this->getUser()]);
        if (!$customer){
            //doit retouner json
            return $this->json(["Uuid"=>"Not found"], 404);
        }
        $em->remove($customer);
        $em->flush();
        return $this->json("", 204);
        
    }
}
