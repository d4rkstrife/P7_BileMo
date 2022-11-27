<?php

namespace App\Serializer;

use App\Entity\Phone;
use App\Versioning\Transformer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PhoneNormalizer implements NormalizerInterface
{
    public function __construct(private UrlGeneratorInterface $router, private ObjectNormalizer $normalizer, private Transformer $transformer)
    {
    }

    public function normalize($phone, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($phone, $format, $context);

        $datas = [];
        $datas['_link']['self'] = $this->router->generate('app_product_details', [
            'uuid' => $phone->getUuid(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $datas['name'] = $data['name'];


        $datas['price'] = $data['price'];
        $datas['brand'] = $data['brand'];
        $datas['description'] = $data['description'];
        $datas['uuid'] = $data['uuid'];
        $datas['createdAt'] = $data['createdAt'];
        $datas = $this->transformer->transform($datas);
        return $datas;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Phone;
    }
}
