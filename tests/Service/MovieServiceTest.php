<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Service;

use App\Factory\MovieFactory;
use App\Repository\MovieRepository;
use App\Service\MovieService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

class MovieServiceTest extends WebTestCase
{
    use Factories;

    protected MovieFactory $movieFactory;
    protected MovieRepository $movieRepository;
    protected MovieService $movieService;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->movieFactory = $container->get(MovieFactory::class);
        $this->movieRepository = $container->get(MovieRepository::class);
        $this->movieService = $container->get(MovieService::class);
    }

    public function testFind_ReturnsMovie(): void
    {
        $movie = $this->movieFactory->createOne();

        $this->assertEquals($movie, $this->movieService->find($movie->getId()));
    }

    public function testFind_ThrowsResourceNotFoundException_IfMovieDoesntExist(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        $this->movieService->find(new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6'));
    }

    public function testFindAll_ReturnsAllMovies(): void
    {
        $movies = $this->movieFactory->createMany(2);

        $this->assertEquals($movies, $this->movieService->findAll());
    }

    public function testCreate_CreatesMovie(): void
    {
        $title = 'Avatar';
        $description = 'Avatar is a 2009 science fiction film...';
        $durationInMinutes = 162;
        $releaseDate = '2009-12-25';
        $genre = 'Science Fiction';

        $movie = $this->movieService->create([
            'title' => $title,
            'description' => $description,
            'durationInMinutes' => $durationInMinutes,
            'releaseDate' => $releaseDate,
            'genre' => $genre,
        ]);

        $this->assertTrue(Uuid::isValid($movie->getId()));
        $this->assertEquals($title, $movie->getTitle());
        $this->assertEquals($description, $movie->getDescription());
        $this->assertEquals($durationInMinutes, $movie->getDurationInMinutes());
        $this->assertEquals(new DateTime($releaseDate), $movie->getReleaseDate());

        $this->assertEquals($genre, $movie->getGenre()->getName());
        $this->assertCount(1, $movie->getGenre()->getMovies());

        $this->assertCount(1, $this->movieRepository->findAll());
    }

    public function testUpdate_UpdatesMovie(): void
    {
        $movie = $this->movieFactory->createOne();

        $title = 'Avatar';
        $description = 'Avatar is a 2009 science fiction film...';
        $durationInMinutes = 162;
        $releaseDate = '2009-12-25';
        $genre = 'Science Fiction';

        $this->movieService->update($movie->getId(), [
            'title' => $title,
            'description' => $description,
            'durationInMinutes' => $durationInMinutes,
            'releaseDate' => $releaseDate,
            'genre' => $genre,
        ]);

        $this->assertEquals($title, $movie->getTitle());
        $this->assertEquals($description, $movie->getDescription());
        $this->assertEquals($durationInMinutes, $movie->getDurationInMinutes());
        $this->assertEquals(new DateTime($releaseDate), $movie->getReleaseDate());

        $this->assertEquals($genre, $movie->getGenre()->getName());
        $this->assertCount(1, $movie->getGenre()->getMovies());
    }

    public function testDelete_DeletesMovie(): void
    {
        $movie = $this->movieFactory->createOne();

        $this->movieService->delete($movie->getId());

        $this->assertCount(0, $this->movieRepository->findAll());
    }
}
