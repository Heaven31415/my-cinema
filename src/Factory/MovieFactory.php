<?php

namespace App\Factory;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use DateTime;
use Exception;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

use function Zenstruck\Foundry\lazy;

/**
 * @extends ModelFactory<Movie>
 *
 * @method        Movie|Proxy create(array|callable $attributes = [])
 * @method static Movie|Proxy createOne(array $attributes = [])
 * @method static Movie|Proxy find(object|array|mixed $criteria)
 * @method static Movie|Proxy findOrCreate(array $attributes)
 * @method static Movie|Proxy first(string $sortedField = 'id')
 * @method static Movie|Proxy last(string $sortedField = 'id')
 * @method static Movie|Proxy random(array $attributes = [])
 * @method static Movie|Proxy randomOrCreate(array $attributes = [])
 * @method static MovieRepository|RepositoryProxy repository()
 * @method static Movie[]|Proxy[] all()
 * @method static Movie[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Movie[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Movie[]|Proxy[] findBy(array $attributes)
 * @method static Movie[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Movie[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 */
final class MovieFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    protected function getDefaults(): array
    {
        return [
            'description' => self::faker()->text(),
            'durationInMinutes' => self::faker()->numberBetween(60, 180),
            'genre' => lazy(fn() => GenreFactory::random()),
            'releaseDate' => new DateTime(self::faker()->date()),
            'title' => ucfirst(self::faker()->unique()->word()),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Movie::class;
    }
}
