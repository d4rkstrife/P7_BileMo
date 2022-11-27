<?php

namespace App\Versioning\Version;

class ConverterV11
{
    public function transform($datas): array
    {
        $datas['price'] = $datas['price'].'€';
        return $datas;
    }
}