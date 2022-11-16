<?php

namespace App\Controller;

use App\Entity\Phone;
use DateTime;
use App\Entity\Customer;
use App\Entity\Reseller;
use App\Service\Paginator;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Symfony\Component\Uid\Uuid;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

class CustomerController extends AbstractController
//last version
{

    public function __construct(
        private CacheInterface $cache,
        private CustomerRepository $customerRepository,
        private TagAwareCacheInterface $myCachePool
    ) {
    }


    #[Operation([
        'summary' => 'List all the customers for the connected reseller.'
    ])]
    #[OA\Parameter(
        name: 'page'
    )]
    #[OA\Response(
        response: 200,
        description: "Returns all user's customers",
        content: new OA\JsonContent(
            example: [
                "_pagination" => [
                    "current_page_number" => 1,
                    "number_items_per_page" => 2,
                    "total_items_count" => 3,
                    "last_page_link" => "http://localhost:3000/api/customers/?page=2",
                    "next_page_link" => "http://localhost:3000/api/customers/?page=2"
                ],
                "items" => [
                    [
                        "_link" => [
                            "self" => "http://localhost:3000/api/customers/80cb47df-a159-4633-865d-1b5524134b9e",
                            "create" => "http://localhost:3000/api/customers",
                            "modify" => "http://localhost:3000/api/customers/80cb47df-a159-4633-865d-1b5524134b9e",
                            "delete" => "http://localhost:3000/api/customers/80cb47df-a159-4633-865d-1b5524134b9e"
                        ],
                        "firstName" => "Gaston",
                        "lastName" => "Satterfield",
                        "adress" => "Labore architecto sed rem adipisci at minima. Et at expedita aut sit eum. Sapiente aperiam in quos officiis deleniti error.",
                        "reseller" => "repellendus",
                        "uuid" => "80cb47df-a159-4633-865d-1b5524134b9e",
                        "email" => "katlyn78@nienow.com",
                        "createdAt" => "1999-08-17T23:49:38+00:00"
                    ],
                    [
                        "_link" => [
                            "self" => "http://localhost:3000/api/customers/e60fa6b9-255d-4aa5-8eda-179acbf54b7f",
                            "create" => "http://localhost:3000/api/customers",
                            "modify" => "http://localhost:3000/api/customers/e60fa6b9-255d-4aa5-8eda-179acbf54b7f",
                            "delete" => "http://localhost:3000/api/customers/e60fa6b9-255d-4aa5-8eda-179acbf54b7f"
                        ],
                        "firstName" => "Estella",
                        "lastName" => "Kautzer",
                        "adress" => "A quia aut optio eaque et qui. Et non enim nisi esse facere corporis aut.",
                        "reseller" => "repellendus",
                        "uuid" => "e60fa6b9-255d-4aa5-8eda-179acbf54b7f",
                        "email" => "hermina17@hotmail.com",
                        "createdAt" => "1985-12-09T05:37:56+00:00"
                    ]
                ]
            ]
        )

    )]
    #[OA\Tag(name: 'Customers')]
    #[Route('/api/customers/', name: 'app_customer', methods: ['GET'])]
    public function readAll(Paginator $paginator, Request $request): Response
    {
        /** @var Reseller $user */
        $user = $this->getUser();


        return $this->myCachePool->get(
            'products_' . $user->getUUid() . '_' . $request->get('page', 1),
            function (ItemInterface $item) use ($user, $paginator) {
                $paginator->createPagination(
                    Customer::class,
                    ['reseller' => $user],
                    ['createdAt' => "desc"],
                    'app.customer_per_page'
                );
                $item->expiresAfter(3600);
                $item->tag($user->getUUid() . '_items');
                if ($paginator->getDatas() === null) {
                    return $this->json(['error' => 'Aucun client trouvé'], 404);
                }

                return $this->json($paginator, 200, context: [
                    'callbacks' => [
                        'reseller' => function ($reseller) {
                            return $reseller->getCompany();
                        }
                    ],
                    'route' => 'app_customer'
                ]);
            }
        );
    }


    #[Operation([
        'summary' => 'Add a new customer.'
    ])]
    #[OA\Response(
        response: 201,
        description: "Return the new customer",
        content: new OA\JsonContent(
            example: [
"_link"=> [
"self"=> "http://localhost:3000/api/customers/dee93ecc-dfce-409d-8e73-7e7643e62c31",
"create"=> "http://localhost:3000/api/customers",
"modify"=> "http://localhost:3000/api/customers/dee93ecc-dfce-409d-8e73-7e7643e62c31",
"delete"=> "http://localhost:3000/api/customers/dee93ecc-dfce-409d-8e73-7e7643e62c31"
],
"firstName"=> "firstName",
    "lastName"=> "lastName",
    "adress"=> "customer's adress",
    "reseller"=> [
    "company"=> "repellendus"
    ],
    "uuid"=> "dee93ecc-dfce-409d-8e73-7e7643e62c31",
    "email"=> "customer@mail.com",
    "createdAt"=> "2022-11-13T06:18:19+00:00"
            ]
        )

    )]
    #[OA\Response(
        response: 400,
        description: "Bad request"
    )]
    #[OA\RequestBody(
        description: "Form to create a new customer",
        content: new OA\JsonContent(
            example: [
                "firstName" => "",
                "lastName" => "",
                "adress" => "",
                "email" => ""
            ]
        )
    )]
    #[OA\Tag(name: 'Customers')]
    #[Route('/api/customers', name: 'app_customer_create', methods: ['POST'])]
    public function addOne(Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        if (!$request->getContent()) {
            return new Response(
                '
            Le formulaire doit être présenté comme suit:
            {
                "firstName":"",
                "lastName":"",
                "adress":"",
                "email":""
            }
            ', 400
            );
        }
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        if ($customer->getAdress() === null || $customer->getFirstName() === null || $customer->getLastName(
            ) === null || $customer->getEmail() === null) {
            return new Response(
                '
            Le formulaire doit être présenté comme suit:
            {
                "firstName":"",
                "lastName":"",
                "adress":"",
                "email":""
            }
            ', 400
            );
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
        $this->customerRepository->add($customer);
        $this->myCachePool->invalidateTags([$this->getUser()->getUUid() . '_items']);
        return $this->json($customer, 201, context: ['groups' => 'customer:read']);
    }


    #[Operation([
        'summary' => 'Return a customer details.'
    ])]
    #[OA\Response(
        response: 200,
        description: "Return the customer's details",
        content: new OA\JsonContent(
            example: [
                "_link" => [
                    "self" => "http://localhost:3000/api/customers/80cb47df-a159-4633-865d-1b5524134b9e",
                    "create" => "http://localhost:3000/api/customers",
                    "modify" => "http://localhost:3000/api/customers/80cb47df-a159-4633-865d-1b5524134b9e",
                    "delete" => "http://localhost:3000/api/customers/80cb47df-a159-4633-865d-1b5524134b9e"
                ],
                "firstName" => "Gaston",
                "lastName" => "Satterfield",
                "adress" => "Labore architecto sed rem adipisci at minima. Et at expedita aut sit eum. Sapiente aperiam in quos officiis deleniti error.",
                "reseller" => [
                    "company" => "repellendus"
                ],
                "uuid" => "80cb47df-a159-4633-865d-1b5524134b9e",
                "email" => "katlyn78@nienow.com",
                "createdAt" => "1999-08-17T23:49:38+00:00"

            ]
        )

    )]
    #[OA\Tag(name: 'Customers')]
    #[Route('/api/customers/{uuid}', name: 'app_customer_details', methods: ['GET'])]
    public function customerDetails(Uuid $uuid): Response
    {
        return $this->cache->get(
            'customer_' . $uuid . '_reseller_' . $this->getUser()->getUuid(),
            function (ItemInterface $item) use ($uuid) {
                $item->expiresAfter(3600);
                $customer = $this->customerRepository->findOneBy(['uuid' => $uuid, 'reseller' => $this->getUser()]);
                if (!$customer) {
                    return $this->json(["error" => "Not found"], 404);
                }
                return $this->json($customer, 200, context: ['groups' => 'customer:read', 'type' => 'details']);
            }
        );
    }


    #[Operation([
        'summary' => 'Modifiate a customer.'
    ])]
    #[OA\RequestBody(
        description: "Form to modifiate a customer",
        content: new OA\JsonContent(
            example: [
                "firstName" => "",
                "lastName" => "",
                "adress" => "",
                "email" => ""
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Return the modified customer",
        content: new OA\JsonContent(
            example: [
                "_link" => [
                    "self" => "http://localhost:3000/api/customers/80cb47df-a159-4633-865d-1b5524134b9e",
                    "create" => "http://localhost:3000/api/customers",
                    "modify" => "http://localhost:3000/api/customers/80cb47df-a159-4633-865d-1b5524134b9e",
                    "delete" => "http://localhost:3000/api/customers/80cb47df-a159-4633-865d-1b5524134b9e"
                ],
                "firstName" => "Gaston",
                "lastName" => "Satterfield",
                "adress" => "Labore architecto sed rem adipisci at minima. Et at expedita aut sit eum. Sapiente aperiam in quos officiis deleniti error.",
                "reseller" => [
                    "company" => "repellendus"
                ],
                "uuid" => "80cb47df-a159-4633-865d-1b5524134b9e",
                "email" => "katlyn78@nienow.com",
                "createdAt" => "1999-08-17T23:49:38+00:00"

            ]
        )

    )]
    #[OA\Tag(name: 'Customers')]
    #[Route('/api/customers/{uuid}', name: 'app_customer_modifiate', methods: ['PUT'])]
    public function customerModification(
        Uuid $uuid,
        EntityManagerInterface $entityManager,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        $oldCustomer = $this->customerRepository->findOneBy(['uuid' => $uuid, 'reseller' => $this->getUser()]);

        if (!$oldCustomer) {
            //doit retouner json
            return $this->json(["error" => "Not found"], 404);
        }

        if (!$request->getContent()) {
            return new Response(
                '
            Le formulaire doit être présenté comme suit:
            {
                "firstName":"",
                "lastName":"",
                "adress":"",
                "email":""
            }
            ', 400
            );
        }
        $customer = $serializer->deserialize(
            $request->getContent(),
            Customer::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $oldCustomer]
        );

        $exceptions = $validator->validate($customer);

        if (count($exceptions) !== 0) {
            $violations = [];
            foreach ($exceptions as $violation) {
                $violations[] = $violation->getMessage();
            }
            return $this->json($violations, 422);
        }

        $entityManager->flush();
        $this->cache->delete('customer_' . $uuid . '_reseller_' . $this->getUser()->getUuid());
        $this->myCachePool->invalidateTags([$this->getUser()->getUUid() . '_items']);

        return $this->json($customer, 200, context: ['groups' => 'customer:read']);
    }


    #[Operation([
        'summary' => 'Delete a customer.'
    ])]
    #[OA\Response(
        response: 204,
        description: "Return delete confirmation message",

    )]
    #[OA\Tag(name: 'Customers')]
    #[Route('/api/customers/{uuid}', name: 'app_customer_delete', methods: ['DELETE'])]
    public function customerDelete(Uuid $uuid, EntityManagerInterface $em): Response
    {
        $customer = $this->customerRepository->findOneBy(['uuid' => $uuid, 'reseller' => $this->getUser()]);
        if (!$customer) {
            return $this->json(["error" => "Not found"], 404);
        }
        $em->remove($customer);
        $em->flush();
        $this->cache->delete('customer_' . $uuid . '_reseller_' . $this->getUser()->getUuid());
        $this->myCachePool->invalidateTags([$this->getUser()->getUUid() . '_items']);
        return $this->json("", 204);
    }
}
