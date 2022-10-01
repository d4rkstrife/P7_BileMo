<?php

namespace App\Versioning\Version;

class ConverterV10
{
    public function transform($datas): array
    {
        unset($datas['price']);
        return $datas;
    }
}