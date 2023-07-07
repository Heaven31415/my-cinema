<?php

namespace App\Factory;

use App\Entity\Hall;
use App\Repository\HallRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Hall>
 *
 * @method        Hall|Proxy create(array|callable $attributes = [])
 * @method static Hall|Proxy createOne(array $attributes = [])
 * @method static Hall|Proxy find(object|array|mixed $criteria)
 * @method static Hall|Proxy findOrCreate(array $attributes)
 * @method static Hall|Proxy first(string $sortedField = 'id')
 * @method static Hall|Proxy last(string $sortedField = 'id')
 * @method static Hall|Proxy random(array $attributes = [])
 * @method static Hall|Proxy randomOrCreate(array $attributes = [])
 * @method static HallRepository|RepositoryProxy repository()
 * @method static Hall[]|Proxy[] all()
 * @method static Hall[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Hall[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Hall[]|Proxy[] findBy(array $attributes)
 * @method static Hall[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Hall[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 */
final class HallFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'capacity' => self::faker()->randomElement([25, 50, 100, 200]),
            'name' => self::faker()->word(),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Hall::class;
    }
}
