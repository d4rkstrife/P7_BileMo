<?php

namespace App\ParamConverter;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

class UuidParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration)
    {
        //dd('tutu uuid');
        $param = $configuration->getName();
        
        $value = $request->attributes->get($param);

        $uuid = Uuid::fromString($value);

        $request->attributes->set($param, $uuid);
    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === Uuid::class;
    }
}
