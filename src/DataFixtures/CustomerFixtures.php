<?php

namespace App\DataFixtures;

use App\Factory\CustomerFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        CustomerFactory::new()->createMany(20);
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            ResellerFixtures::class,
        ];
    }
}
