<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Service\Paginator;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Uid\Uuid;
use App\Repository\PhoneRepository;
use App\Serializer\PhoneNormalizer;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

class ProductController extends AbstractController
{
    public function __construct(private CacheInterface $cache, private PhoneRepository $phoneRepository)
    {
    }


    #[Operation([
        'summary' => 'List all the products.'
    ])]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 200,
        description: 'Returns all the products',
        content: new OA\JsonContent(
            example: [
                "_pagination" => [
                    "current_page_number" => 1,
                    "number_items_per_page" => 2,
                    "total_items_count" => 20,
                    "last_page_link" => "http://localhost:3000/api/products?page=10",
                    "next_page_link" => "http://localhost:3000/api/products?page=2"
                ],
                "items" => [
                    [
                        "_link" => [
                            "self" => "http://localhost:3000/api/products/9ba536be-d09f-4ca8-9aa8-039b356cd846"
                        ],
                        "name" => "qui",
                        "brand" => "doloribus",
                        "description" => "Pariatur omnis consequatur sunt asperiores ea harum cumque cum dolorem nobis molestiae reiciendis voluptas reprehenderit accusamus autem et ipsa dolor asperiores.",
                        "uuid" => "9ba536be-d09f-4ca8-9aa8-039b356cd846",
                        "createdAt" => "2021-06-06T14:25:32+00:00"
                    ],
                    [
                        "_link" => [
                            "self" => "http://localhost:3000/api/products/2b3e7a88-0e9d-429a-a521-9650f52af57a"
                        ],
                        "name" => "deserunt",
                        "brand" => "dolores",
                        "description" => "Accusamus qui optio sunt cumque ea et quia repellendus et minima omnis aliquam enim dignissimos molestiae magnam temporibus hic esse suscipit et et nemo aut eos ipsa quia in animi et tempore reprehenderit aliquid ut ipsum.",
                        "uuid" => "2b3e7a88-0e9d-429a-a521-9650f52af57a",
                        "createdAt" => "2019-09-10T08:51:33+00:00"
                    ]
                ]

            ]
        )
    )]
    #[OA\Parameter(
        name: 'page'
    )]
    #[OA\Tag(name: 'Product')]
    #[Route('api/products', name: 'app_product', methods: ['GET'])]
    public function allProducts(Paginator $paginator, Request $request): Response
    {
        $version = $request->headers->get('version', "1.2");
        return $this->cache->get(
            'products_page=' . $request->get('page', 1) . ' version=' . $version,
            function (ItemInterface $item) use ($paginator, $version) {
                $item->expiresAfter(3600);
                $paginator->createPagination(Phone::class, [], ['createdAt' => "desc"], 'app.phone_per_page');
                return $this->json($paginator, 200, context: ['route' => 'app_product', 'version' => $version]);
            }
        );
    }


    #[Operation([
        'summary' => 'See one particular product.'
    ])]
    #[OA\Response(
        response: 200,
        description: 'Returns the details of a product',
        content: new OA\JsonContent(
            example: [
                "_link" => [
                    "self" => "http://localhost:3000/api/products/2b3e7a88-0e9d-429a-a521-9650f52af57a"
                ],
                "name" => "deserunt",
                "price" => "904",
                "brand" => "dolores",
                "description" => "Accusamus qui optio sunt cumque ea et quia repellendus et minima omnis aliquam enim dignissimos molestiae magnam temporibus hic esse suscipit et et nemo aut eos ipsa quia in animi et tempore reprehenderit aliquid ut ipsum.",
                "uuid" => "2b3e7a88-0e9d-429a-a521-9650f52af57a",
                "createdAt" => "2019-09-10T08:51:33+00:00"
            ]
        )

    )]
    #[OA\Response(
        response: 404,
        description: "Not found"
    )]
    #[OA\Tag(name: 'Product')]
    #[Route('/api/products/{uuid}', name: 'app_product_details', methods: ['GET'])]
    public function productDetails(Uuid $uuid, Request $request): Response
    {
        $version = $request->headers->get('version', "1.2");
        return $this->cache->get(
            'product=' . $uuid . ' version=' . $version,
            function (ItemInterface $item) use ($uuid, $version) {
                $item->expiresAfter(3600);
                $phone = $this->phoneRepository->findOneBy(['uuid' => $uuid]);

                if (!$phone) {
                    return $this->json(["Uuid" => "Not found"], 404);
                }
                return $this->json($phone, 200, context: ['version' => $version]);
            }
        );
    }
}
