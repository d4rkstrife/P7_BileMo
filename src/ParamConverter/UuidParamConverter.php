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

        if (!$request->attributes->has($param)) {
            return;
        }

        $value = $request->attributes->get($param);

        if (!$value) {
            if ($configuration->isOptional()) {
                return null;
            } else {
                throw new NotFoundHttpException('The UUID not found in attributes of request.');
            }
        }

        $uuid = Uuid::fromString($value);

        $request->attributes->set($param, $uuid);
    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === Uuid::class;
    }
}
