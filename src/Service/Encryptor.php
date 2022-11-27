<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class Encryptor
{
    private string $key;
    private string $nonce;

    public function __construct(private ContainerBagInterface $params)
    {
        $this->key=hex2bin($this->params->get('app.sodium_key'));
        $this->nonce=hex2bin($this->params->get('app.sodium_nonce'));
    }
    public function encrypt(string $string):string
    {

        return base64_encode(sodium_crypto_secretbox($string,$this->nonce,$this->key));
    }
    public function decrypt(string $string):string
    {
        return sodium_crypto_secretbox_open(base64_decode($string),$this->nonce,$this->key);
    }
}