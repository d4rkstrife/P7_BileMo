<?php

namespace App\ParamConverter;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

class UuidParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $param = $configuration->getName();
        if(!$request->attributes->has($param)){
            return false;
        }
        $value = $request->attributes->get($param);
        if($value === '' && $configuration->isOptional()){
            $request->attributes->set($param, null);
            return true;
        }

        $uuid = Uuid::fromString($value);

        $request->attributes->set($param, $uuid);
        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === Uuid::class;
    }
}
