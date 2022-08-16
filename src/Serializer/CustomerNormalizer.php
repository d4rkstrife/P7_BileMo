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
        //dd($context);

        $data['_link']['self'] = $this->router->generate('customer_details', [
            'uuid' => $customer->getUuid(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $data['_link']['create'] = $this->router->generate('addCustomer', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $data['_link']['modify'] = $this->router->generate('customerModification', [
            'uuid' => $customer->getUuid(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
       
        $data['_link']['delete'] = $this->router->generate('customerDelete', [
            'uuid' => $customer->getUuid(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);   

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Customer;
    }
}