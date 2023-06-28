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

class MovieServiceTest extends WebTestCase
{
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

    public function testFind_ReturnsMovie_IfItExists(): void
    {
        $movie = $this->movieFactory->create();

        $this->assertEquals($movie, $this->movieService->find($movie->getId()));
    }

    public function testFind_ThrowsResourceNotFoundException_IfMovieDoesntExist(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        $this->movieService->find(new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6'));
    }

    public function testFindAll_ReturnsAllMovies(): void
    {
        $movies = [$this->movieFactory->create(), $this->movieFactory->create()];

        $this->assertEquals($movies, $this->movieService->findAll());
    }

    public function testCreate_CreatesMovie(): void
    {
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'durationInMinutes' => 162,
            'releaseDate' => '2009-12-25',
            'genre' => 'Science Fiction',
        ];

        $movie = $this->movieService->create($data);
        $genre = $movie->getGenre();

        $this->assertEquals('Avatar', $movie->getTitle());
        $this->assertEquals('Avatar is a 2009 science fiction film...', $movie->getDescription());
        $this->assertEquals(162, $movie->getDurationInMinutes());
        $this->assertEquals(new DateTime('2009-12-25'), $movie->getReleaseDate());

        $this->assertEquals('Science Fiction', $genre->getName());
        $this->assertCount(1, $genre->getMovies());

        $this->assertCount(1, $this->movieRepository->findAll());
    }

    public function testCreate_ThrowsResourceNotFoundException_IfGenreDoesntExist(): void
    {
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'durationInMinutes' => 162,
            'releaseDate' => '2009-12-25',
            'genre' => 'there is no genre',
        ];

        $this->expectException(ResourceNotFoundException::class);

        $this->movieService->create($data);
    }

    public function testUpdate_UpdatesMovie(): void
    {
        $movie = $this->movieFactory->create();
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'durationInMinutes' => 162,
            'releaseDate' => '2009-12-25',
            'genre' => 'Science Fiction',
        ];

        $this->movieService->update($movie->getId(), $data);

        $genre = $movie->getGenre();

        $this->assertEquals('Avatar', $movie->getTitle());
        $this->assertEquals('Avatar is a 2009 science fiction film...', $movie->getDescription());
        $this->assertEquals(162, $movie->getDurationInMinutes());
        $this->assertEquals(new DateTime('2009-12-25'), $movie->getReleaseDate());

        $this->assertEquals('Science Fiction', $genre->getName());
        $this->assertCount(1, $genre->getMovies());
    }


    public function testUpdate_ThrowsResourceNotFoundException_IfGenreDoesntExist(): void
    {
        $movie = $this->movieFactory->create();
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'durationInMinutes' => 162,
            'releaseDate' => '2009-12-25',
            'genre' => 'there is no genre',
        ];

        $this->expectException(ResourceNotFoundException::class);

        $this->movieService->update($movie->getId(), $data);
    }

    public function testUpdate_ThrowsResourceNotFoundException_IfMovieDoesntExist(): void
    {
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'durationInMinutes' => 162,
            'releaseDate' => '2009-12-25',
            'genre' => 'Science Fiction',
        ];

        $this->expectException(ResourceNotFoundException::class);

        $this->movieService->update(new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6'), $data);
    }

    public function testDelete_DeletesMovie_IfItExists(): void
    {
        $movie = $this->movieFactory->create();

        $this->movieService->delete($movie->getId());

        $this->assertCount(0, $this->movieRepository->findAll());
    }

    public function testDelete_ThrowsResourceNotFoundException_IfMovieDoesntExist(): void
    {
        $this->expectException(ResourceNotFoundException::class);

        $this->movieService->delete(new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6'));
    }
}
