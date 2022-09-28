<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Service\Paginator;
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

class ProductController extends AbstractController
{
    public function __construct(private CacheInterface $cache, private PhoneRepository $phoneRepository)
    {
    }

    #[Route('api/products', name: 'app_product', methods: ['GET'])]
    public function allProducts(Paginator $paginator, Request $request): Response
    {
        //dd($request->headers->get('version', 1.0));
        $version = $request->headers->get('version', "1.0");
        return $this->cache->get('products' . $request->get('page', 1), function (ItemInterface $item) use ($paginator, $version) {
            $item->expiresAfter(3600);
            $paginator->createPagination(Phone::class, [], ['createdAt' => "desc"], 'app.phoneperpage');
            return $this->json($paginator, 200, context: ['route' => 'app_product', 'version' => $version]);
        });
        
    }

    #[Route('/api/products/{uuid}', name: 'app_product_details', methods: ['GET'])]
    public function productDetails(Uuid $uuid, Request $request): Response
    {
        $version = $request->headers->get('version', "1.0");
        return $this->cache->get($uuid, function (ItemInterface $item) use ($uuid, $version) {
            $item->expiresAfter(3600);
            $phone = $this->phoneRepository->findOneBy(['uuid' => $uuid]);

            if (!$phone) {
                return $this->json(["Uuid" => "Not found"], 404);
            }
            return $this->json($phone, 200, context: ['version' => $version]);
        });
    }
}
