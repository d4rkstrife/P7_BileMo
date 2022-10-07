<?php

namespace App\Controller;

use DateTime;
use App\Entity\Customer;
use App\Entity\Reseller;
use App\Service\Paginator;
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

class CustomerController extends AbstractController
//last version
{

    public function __construct(private CacheInterface $cache, private CustomerRepository $customerRepository, private TagAwareCacheInterface $myCachePool)
    {
    }

    #[Route('/api/customers/', name: 'app_customer', methods: ['GET'])]
    public function readAll(Paginator $paginator, Request $request): Response
    {

        /** @var Reseller $user */
        $user = $this->getUser();


        return $this->myCachePool->get('products_' . $user->getUUid() . '_' . $request->get('page', 1), function (ItemInterface $item) use ($user, $paginator) {
            $paginator->createPagination(Customer::class, ['reseller' => $user], ['createdAt' => "desc"], 'app.customer_per_page');
            $item->expiresAfter(3600);
            $item->tag($user->getUUid() . '_items');
            if ($paginator->getDatas() === null) {
                return $this->json(['error' => 'Aucun client trouvé'], 404);
            }

            return $this->json($paginator, 200, context: [
                'callbacks' => ['reseller' => function ($reseller) {
                    return $reseller->getCompany();
                }],
                'route' => 'app_customer'
            ]);
        });
    }


    #[Route('/api/customers', name: 'app_customer_create', methods: ['POST'])]
    public function addOne(Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
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
            Le formulaire doit être présenté comme suit:
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
        $this->customerRepository->add($customer);
        $this->myCachePool->invalidateTags([$this->getUser()->getUUid() . '_items']);
        return $this->json($customer, 201, context: ['groups' => 'customer:read']);
    }

    #[Route('/api/customers/{uuid}', name: 'app_customer_details', methods: ['GET'])]
    public function customerDetails(Uuid $uuid): Response
    {

        return $this->cache->get('customer_'.$uuid .'_reseller_'. $this->getUser()->getUuid(), function (ItemInterface $item) use ($uuid) {
            $item->expiresAfter(3600);
            $customer = $this->customerRepository->findOneBy(['uuid' => $uuid, 'reseller' => $this->getUser()]);
            if (!$customer) {
                return $this->json(["error" => "Not found"], 404);
            }
            return $this->json($customer, 200, context: ['groups' => 'customer:read', 'type' => 'details']);
        });
    }

    #[Route('/api/customers/{uuid}', name: 'app_customer_modifiate', methods: ['PUT'])]
    public function customerModification(Uuid $uuid, EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $oldCustomer = $this->customerRepository->findOneBy(['uuid' => $uuid, 'reseller' => $this->getUser()]);

        if (!$oldCustomer) {
            //doit retouner json
            return $this->json(["error" => "Not found"], 404);
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
        $this->cache->delete('customer_'.$uuid .'_reseller_'. $this->getUser()->getUuid());
        $this->myCachePool->invalidateTags([$this->getUser()->getUUid() . '_items']);

        return $this->json($customer, 200, context: ['groups' => 'customer:read']);
    }

    #[Route('/api/customers/{uuid}', name: 'app_customer_delete', methods: ['DELETE'])]
    public function customerDelete(Uuid $uuid, EntityManagerInterface $em): Response
    {
        $customer = $this->customerRepository->findOneBy(['uuid' => $uuid, 'reseller' => $this->getUser()]);
        if (!$customer) {
            return $this->json(["error" => "Not found"], 404);
        }
        $em->remove($customer);
        $em->flush();
        $this->cache->delete('customer_'.$uuid .'_reseller_'. $this->getUser()->getUuid());
        $this->myCachePool->invalidateTags([$this->getUser()->getUUid() . '_items']);
        return $this->json("", 204);
    }
}
