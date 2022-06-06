<?php

namespace App\Controller;

use DateTime;
use App\Entity\Reseller;
use App\Repository\ResellerRepository;
use Symfony\Component\Uid\Uuid;
use Doctrine\DBAL\Types\ObjectType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SignUpController extends AbstractController
{
    #[Route('/api/signup', name: 'app_sign_up', methods: ['POST'])]
    public function index(Request $request, SerializerInterface $serializer, ResellerRepository $resellerRepo, ValidatorInterface $validator): Response
    {
        $reseller = $serializer->deserialize($request->getContent(), Reseller::class, 'json');
        $reseller->setCreatedAt(new DateTime());
        $reseller->setUuid(Uuid::v4());
        $exceptions = $validator->validate($reseller);
        // dd($exceptions);
        $resellerRepo->add($reseller);

        return $this->json('ok');
    }
}
