<?php

namespace App\Factory;

use App\Entity\Show;
use App\Repository\ShowRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

use function Zenstruck\Foundry\lazy;

/**
 * @extends ModelFactory<Show>
 *
 * @method        Show|Proxy create(array|callable $attributes = [])
 * @method static Show|Proxy createOne(array $attributes = [])
 * @method static Show|Proxy find(object|array|mixed $criteria)
 * @method static Show|Proxy findOrCreate(array $attributes)
 * @method static Show|Proxy first(string $sortedField = 'id')
 * @method static Show|Proxy last(string $sortedField = 'id')
 * @method static Show|Proxy random(array $attributes = [])
 * @method static Show|Proxy randomOrCreate(array $attributes = [])
 * @method static ShowRepository|RepositoryProxy repository()
 * @method static Show[]|Proxy[] all()
 * @method static Show[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Show[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Show[]|Proxy[] findBy(array $attributes)
 * @method static Show[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Show[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 */
final class ShowFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'hall' => lazy(fn() => HallFactory::new()),
            'movie' => lazy(fn() => MovieFactory::new()),
            'startTime' => self::faker()->dateTime(),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Show::class;
    }
}
