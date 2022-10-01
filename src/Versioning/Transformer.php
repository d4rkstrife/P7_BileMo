<?php

namespace App\Versioning;



use Symfony\Component\HttpFoundation\RequestStack;

class Transformer
{
    public function __construct(private RequestStack $requestStack)
    {

    }
    public function transform(array $datas): array
    {
        $version = $this->requestStack->getCurrentRequest()->headers->get('version');
        $versionTransformer = 'App\Versioning\Version\ConverterV'.$version;
        $transformer = new $versionTransformer();


        return $transformer->transform($datas);
    }
}