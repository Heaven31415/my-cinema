<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Service;

use App\Factory\GenreFactory;
use App\Service\GenreService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class GenreServiceTest extends WebTestCase
{
    protected GenreFactory $genreFactory;
    protected GenreService $genreService;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->genreFactory = $container->get(GenreFactory::class);
        $this->genreService = $container->get(GenreService::class);
    }

    public function testFindByName_ReturnsGenre_IfItExists(): void
    {
        $genre = $this->genreFactory->create();
        $name = $genre->getName();

        $this->assertEquals($genre, $this->genreService->findByName($name));
    }

    public function testFindByName_ThrowsResourceNotFoundException_IfGenreDoesntExist(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        $this->genreService->findByName('there is no name');
    }
}