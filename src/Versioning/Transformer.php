<?php

namespace App\Versioning;



use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Transformer
{
    public function __construct(private RequestStack $requestStack,private ContainerBagInterface $params)
    {

    }
    public function transform(array $datas): array
    {
        $version = $this->requestStack->getCurrentRequest()->headers->get('version', "1.2");
        if(array_key_exists($version,$this->params->get("app.previous_version"))){
            $versionTransformer = $this->params->get("app.previous_version")[$version];
            $transformer = new $versionTransformer();
            return $transformer->transform($datas);
        }
        return $datas;
    }
}