<?php

namespace App\Factory;

use App\Entity\Reseller;
use Zenstruck\Foundry\Proxy;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\ModelFactory;
use App\Repository\ResellerRepository;
use DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Reseller>
 *
 * @method static Reseller|Proxy createOne(array $attributes = [])
 * @method static Reseller[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Reseller|Proxy find(object|array|mixed $criteria)
 * @method static Reseller|Proxy findOrCreate(array $attributes)
 * @method static Reseller|Proxy first(string $sortedField = 'id')
 * @method static Reseller|Proxy last(string $sortedField = 'id')
 * @method static Reseller|Proxy random(array $attributes = [])
 * @method static Reseller|Proxy randomOrCreate(array $attributes = [])
 * @method static Reseller[]|Proxy[] all()
 * @method static Reseller[]|Proxy[] findBy(array $attributes)
 * @method static Reseller[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Reseller[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ResellerRepository|RepositoryProxy repository()
 * @method Reseller|Proxy create(array|callable $attributes = [])
 */
final class ResellerFactory extends ModelFactory
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'uuid' => Uuid::v4(),
            'roles' => [],
            'password' => 'Password1!',
            'company' => self::faker()->unique()->word(),
            'email' => self::faker()->unique()->email(),
            'createdAt' => self::faker()->dateTime(), // TODO add DATETIME ORM type manually
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            ->afterInstantiate(function (Reseller $reseller): void {
                $reseller->setPassword($this->userPasswordHasher->hashPassword($reseller, $reseller->getPassword()));
            });
    }

    protected static function getClass(): string
    {
        return Reseller::class;
    }
}
