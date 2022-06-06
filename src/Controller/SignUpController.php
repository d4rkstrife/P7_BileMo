<?php

namespace App\Controller;

use App\Entity\Reseller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class SignUpController extends AbstractController
{
    #[Route('/api/signup', name: 'app_sign_up', methods: ['POST'])]
    public function index(Request $request, SerializerInterface $serializer): Response
    {
        $datas = $request->getContent();

        dd($datas);


        return $this->render('sign_up/index.html.twig', [
            'controller_name' => 'SignUpController',
        ]);
    }
}
