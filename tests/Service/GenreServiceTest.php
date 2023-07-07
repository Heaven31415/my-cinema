<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Service;

use App\Factory\GenreFactory;
use App\Service\GenreService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Zenstruck\Foundry\Test\Factories;

class GenreServiceTest extends WebTestCase
{
    use Factories;

    protected GenreService $genreService;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->genreService = $container->get(GenreService::class);
    }

    public function testFindByName_ReturnsGenre_IfItExists(): void
    {
        $genre = GenreFactory::createOne();
        $name = $genre->getName();

        $this->assertEquals($genre->object(), $this->genreService->findByName($name));
    }

    public function testFindByName_ThrowsResourceNotFoundException_IfGenreDoesntExist(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        $this->genreService->findByName('there is no name');
    }
}