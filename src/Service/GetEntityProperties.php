<?php

namespace App\Service;

class GetEntityProperties
{
    public function getProperties(object $entity, string $attributeSearch): array
    {
        $toto = new \ReflectionObject($entity);
        $propertiesArray = [];
        foreach ($toto->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                //dump(substr($attribute->getName(),(int) strrpos($attribute->getName(),'\\')+1), $attribute->getName());
                if (substr($attribute->getName(),(int) strrpos($attribute->getName(),'\\')+1) === $attributeSearch) {
                    //dump($attribute->getName());
                    //dump('match', $attribute);
                    $propertiesArray[] = $property;
                }
            }
        }
        return $propertiesArray;
    }

}