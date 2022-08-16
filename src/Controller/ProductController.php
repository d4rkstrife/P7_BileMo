<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Service\Paginator;
use Symfony\Component\Uid\Uuid;
use App\Repository\PhoneRepository;
use App\Serializer\PhoneNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
   /* public function __construct(PhoneRepository $phoneRepo, PhoneNormalizer $phoneNormalizer)
    {
        $this->phoneRepo = $phoneRepo;
        $this->phoneNormalizer = $phoneNormalizer;
    }*/

    #[Route('api/products', name: 'app_product', methods: ['GET'])]
    public function allProducts(Paginator $paginator): Response
    {
        $paginator->createPagination(Phone::class, [], ['createdAt' => "desc"], 'app.phoneperpage');
        //return $this->json($paginator->getDatas(), 200);
        return $this->json($paginator, 200, context:['route'=>'app_product']);
    }

    #[Route('/api/products/{uuid}', name: 'product_details', methods: ['GET'])]
    public function productDetails(Uuid $uuid, PhoneRepository $phoneRepository): Response
    {
        $phone = $phoneRepository->findOneBy(['uuid'=>$uuid]);
        
        if(!$phone){
            return $this->json(["Uuid"=>"Not found"], 404); 
        }
       return $this->json($phone, 200);
    }
}
