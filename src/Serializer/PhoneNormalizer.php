<?php

namespace App\Serializer;

use App\Entity\Phone;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PhoneNormalizer implements NormalizerInterface
{
    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize($phone, string $format = null, array $context = [])
    {        
        $data = $this->normalizer->normalize($phone, $format, $context);
        // Here, add, edit, or delete some data: 
           $data['_link']['self'] = $this->router->generate('product_details', [
            'uuid' => $phone->getUuid(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);       
           
        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Phone;
    }
}