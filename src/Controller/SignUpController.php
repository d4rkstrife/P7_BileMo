<?php

namespace App\Controller;

use DateTime;
use App\Entity\Reseller;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes\Tag;
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
use OpenApi\Attributes as OA;

class SignUpController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    #[Operation([
        'summary' =>"Reseller sign up"
    ])]
    #[OA\Tag(name: "Reseller")]
    #[OA\Response(
        response: 201,
        description: "Return the created reseller",
        content: new OA\JsonContent(
            example: [
                "uuid" => "cd88bc0d-129e-46d9-ac7f-76f6925f3285",
                "email" => "sdsf@mail.com",
                "createdAt" => "2022-11-16T04:42:51+00:00"
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Error in the form",
        content: new OA\JsonContent(
            example: [
                "email" => "",
                "password" => "",
                "company" => ""
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Error: Unprocessable Entity"

    )]
    #[OA\RequestBody(
        description: "Form to sign up",
        content: new OA\JsonContent(
            example: [
                "email" => "",
                "password" => "",
                "company" => ""
            ]
        )
    )]
    #[Route('/api/signup', name: 'app_sign_up', methods: ['POST'])]
    #[Security([])]
    public function index(
        Request $request,
        SerializerInterface $serializer,
        ResellerRepository $resellerRepo,
        ValidatorInterface $validator
    ): Response {
        if (!$request->getContent()) {
            return $this->json(["error"=>"See the documentation for the form"],400);
        }
        $reseller = $serializer->deserialize($request->getContent(), Reseller::class, 'json');
        if ($reseller->getEmail() === null || $reseller->getPassword() === null || $reseller->getCompany() === null) {
            return $this->json(["error"=>"See the documentation for the form"],400);

        }

        $reseller->setCreatedAt(new DateTime());
        $reseller->setUuid(Uuid::v4());
        $exceptions = $validator->validate($reseller);
        //dd($exceptions->get(0));
        if (count($exceptions) !== 0) {
            $violations = [];
            foreach ($exceptions as $violation) {
                $violations[] = $violation->getMessage();
            }
            return $this->json($violations, 422);
        }
        $reseller->setPassword($this->userPasswordHasher->hashPassword($reseller, $reseller->getPassword()));

        $resellerRepo->add($reseller);

        return $this->json($reseller, 201, context: ['groups' => 'reseller:read']);
    }
}
