<?php

namespace App\Serializer;

use App\Entity\Customer;
use App\Service\Paginator;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaginatorNormalizer implements NormalizerInterface
{
    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        //dd($object, $format, $context);
        $datasPaginator = [];
        $itemsDatas = [];
        $data = $this->normalizer->normalize($object, $format, $context);
        $itemsDatas = $data['datas'];
        $datasPaginator['_pagination']['current_page_number'] = $data['requestPage'];
        $datasPaginator['_pagination']['number_items_per_page'] = $data['numberOfItems'];
        $datasPaginator['_pagination']['total_items_count'] = $data['maxOfItems'];
        $datasPaginator['items'] = $itemsDatas;

        //dd($datasPaginator);
        // Here, add, edit, or delete some data: 
        //dd($data);
        //dd($context);
        
    

        return $datasPaginator;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Paginator;
    }
}