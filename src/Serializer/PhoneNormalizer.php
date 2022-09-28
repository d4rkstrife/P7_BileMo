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
        // dd($context['version']);
        $datas = [];
        $datas['_link']['self'] = $this->router->generate('app_product_details', [
            'uuid' => $phone->getUuid(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        //dd($data);
        $datas['name'] = $data['name'];
        if ($context['version'] === "1.0"){
            $datas['price'] = $data['price'];
        } elseif ($context['version'] === "1.1"){
            $datas['price'] = $data['price'] . "€";
        }
        //$datas['price'] = $data['price'];
        $datas['brand'] = $data['brand'];
        $datas['description'] = $data['description'];
        $datas['uuid'] = $data['uuid'];
        $datas['createdAt'] = $data['createdAt'];
        return $datas;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Phone;
    }
}
