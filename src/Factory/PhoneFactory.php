<?php

namespace App\Factory;

use App\Entity\Phone;
use Zenstruck\Foundry\Proxy;
use Symfony\Component\Uid\Uuid;
use App\Repository\PhoneRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Phone>
 *
 * @method static Phone|Proxy createOne(array $attributes = [])
 * @method static Phone[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Phone|Proxy find(object|array|mixed $criteria)
 * @method static Phone|Proxy findOrCreate(array $attributes)
 * @method static Phone|Proxy first(string $sortedField = 'id')
 * @method static Phone|Proxy last(string $sortedField = 'id')
 * @method static Phone|Proxy random(array $attributes = [])
 * @method static Phone|Proxy randomOrCreate(array $attributes = [])
 * @method static Phone[]|Proxy[] all()
 * @method static Phone[]|Proxy[] findBy(array $attributes)
 * @method static Phone[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Phone[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PhoneRepository|RepositoryProxy repository()
 * @method Phone|Proxy create(array|callable $attributes = [])
 */
final class PhoneFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'name' => self::faker()->unique()->word(),
            'price' => self::faker()->numberBetween($min = 900, $max = 1020),
            'brand' => self::faker()->word(),
            'description' => self::faker()->sentence($nbWords = 30, $variableNbWords = true),
            'uuid' => Uuid::v4(), // TODO add UUID ORM type manually
            'createdAt' => self::faker()->dateTime(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Phone $phone): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Phone::class;
    }
}
