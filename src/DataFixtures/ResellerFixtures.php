<?php

namespace App\DataFixtures;

use App\Factory\ResellerFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ResellerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ResellerFactory::new()->createMany(20);
        $manager->flush();
    }
}
