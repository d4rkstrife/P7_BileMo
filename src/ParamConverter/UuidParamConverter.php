<?php

namespace App\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

class UuidParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration)
    {
        // TODO: Implement apply() method.
    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === Uuid::class;
    }
}
