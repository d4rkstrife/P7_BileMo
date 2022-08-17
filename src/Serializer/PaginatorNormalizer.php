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
        //dd($context['route']);
        $datasPaginator = [];
        $itemsDatas = [];
        $data = $this->normalizer->normalize($object, $format, $context);
        $maxPage = $data['maxOfItems'] / $data['numberOfItems'];

        $itemsDatas = $data['datas'];
        $datasPaginator['_pagination']['current_page_number'] = $data['requestPage'];
        $datasPaginator['_pagination']['number_items_per_page'] = $data['numberOfItems'];
        $datasPaginator['_pagination']['total_items_count'] = $data['maxOfItems'];
        if ($data['requestPage'] !== 1) {
            $datasPaginator['_pagination']['first_page_link'] = $this->router->generate($context['route'], [
                'page' => 1,
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }
        if ($data['requestPage'] !== $maxPage) {
            $datasPaginator['_pagination']['last_page_link'] = $this->router->generate($context['route'], [
                'page' => $maxPage,
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }
        if ($data['requestPage'] > 1) {
            $datasPaginator['_pagination']['previous_page_link'] = $this->router->generate($context['route'], [
                'page' => $data['requestPage'] - 1,
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }
        if ($data['requestPage'] < $maxPage) {
            $datasPaginator['_pagination']['next_page_link'] = $this->router->generate($context['route'], [
                'page' => $data['requestPage'] + 1,
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }

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
