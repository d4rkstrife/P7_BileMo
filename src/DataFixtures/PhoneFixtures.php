<?php

namespace App\DataFixtures;

use App\Factory\PhoneFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PhoneFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        PhoneFactory::new()->createMany(20);
        $manager->flush();
    }
}
