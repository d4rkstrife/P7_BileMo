<?php

namespace App\Controller;

use DateTime;
use App\Entity\Reseller;
use Symfony\Component\Uid\Uuid;
use Doctrine\DBAL\Types\ObjectType;
use App\Repository\ResellerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    #[Route('/api/signup', name: 'app_sign_up', methods: ['POST'])]
    public function index(Request $request, SerializerInterface $serializer, ResellerRepository $resellerRepo, ValidatorInterface $validator): Response
    {
        $reseller = $serializer->deserialize($request->getContent(), Reseller::class, 'json');
        $reseller->setCreatedAt(new DateTime());
        $reseller->setUuid(Uuid::v4());
        $exceptions = $validator->validate($reseller);
        //dd($exceptions->get(0));
        if (count($exceptions) !== 0) {
            $violations = [];
            foreach ($exceptions as $violation) {
                $violations[] = $violation->getMessage();
            }
            //à faire
            return $this->json('problème');
        }
        $reseller->setPassword($this->userPasswordHasher->hashPassword($reseller, $reseller->getPassword()));

        $resellerRepo->add($reseller);

        return $this->json($reseller, 201);
    }
}
