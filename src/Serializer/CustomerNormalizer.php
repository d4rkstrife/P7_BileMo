<?php

namespace App\Serializer;

use App\Entity\Customer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CustomerNormalizer implements NormalizerInterface
{
    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize($customer, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($customer, $format, $context);
        // Here, add, edit, or delete some data: 
        //dd($data);
         $data['_link']['self']['href'] = $this->router->generate('customer_details', [
            'uuid' => $customer->getUuid(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $data['_link']['self']['action'] = "GET";

        $data['_link']['modify']['href'] = $this->router->generate('customerModification', [
            'uuid' => $customer->getUuid(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $data['_link']['modify']['action'] = "PUT";
       
        $data['_link']['delete']['href'] = $this->router->generate('customerDelete', [
            'uuid' => $customer->getUuid(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $data['_link']['delete']['action'] = "DELETE";

 //test 3
        //dd($data);
        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Customer;
    }
}