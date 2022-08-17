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
        $datas = [];
        $datas['_link']['self'] = $this->router->generate('app_customer_details', [
            'uuid' => $customer->getUuid(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $datas['_link']['create'] = $this->router->generate('app_customer_create', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $datas['_link']['modify'] = $this->router->generate('app_customer_modifiate', [
            'uuid' => $customer->getUuid(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $datas['_link']['delete'] = $this->router->generate('app_customer_delete', [
            'uuid' => $customer->getUuid(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $datas['firstName'] = $data['firstName'];
        $datas['lastName'] = $data['lastName'];
        $datas['adress'] = $data['adress'];
        $datas['reseller'] = $data['reseller'];
        $datas['uuid'] = $data['uuid'];
        $datas['email'] = $data['email'];
        $datas['createdAt'] = $data['createdAt'];

        return $datas;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Customer;
    }
}
