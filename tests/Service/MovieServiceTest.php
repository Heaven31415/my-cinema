<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Service;

use App\Exception\EntityNotFoundException;
use App\Exception\InvalidDataException;
use App\Factory\MovieFactory;
use App\Repository\MovieRepository;
use App\Service\MovieService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

class MovieServiceTest extends WebTestCase
{
    protected MovieFactory $factory;
    protected MovieRepository $movieRepository;
    protected MovieService $movieService;

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->factory = $container->get(MovieFactory::class);
        $this->movieRepository = $container->get(MovieRepository::class);
        $this->movieService = $container->get(MovieService::class);
    }

    public function testFindReturnsMovieIfItExists(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();

        $foundMovie = $this->movieService->find($id);

        $this->assertEquals($movie, $foundMovie);
    }

    public function testFindThrowsEntityNotFoundExceptionIfMovieDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');

        $this->expectException(EntityNotFoundException::class);

        $this->movieService->find($id);
    }

    public function testFindAllReturnsAllMovies(): void
    {
        $movies = [$this->factory->create(), $this->factory->create()];

        $foundMovies = $this->movieService->findAll();

        $this->assertEquals($movies, $foundMovies);
    }

    public function testCreateCreatesMovieIfDataIsValid(): void
    {
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => 'Science Fiction',
        ];

        $movie = $this->movieService->create($data);

        $this->assertEquals('Avatar', $movie->getTitle());
        $this->assertEquals('Avatar is a 2009 science fiction film...', $movie->getDescription());
        $this->assertEquals(new DateTime('02:42:00'), $movie->getLength());
        $this->assertEquals(new DateTime('2009-12-25'), $movie->getReleaseDate());
        $this->assertCount(1, $this->movieRepository->findAll());
    }

    public function testCreateThrowsInvalidDataExceptionIfDataIsInvalid(): void
    {
        $data = [
            'title' => 1,
            'release_date' => '2009-12-25',
        ];

        $this->expectException(InvalidDataException::class);

        $this->movieService->create($data);

        $this->assertCount(0, $this->movieRepository->findAll());
    }

    public function testCreateThrowsEntityNotFoundExceptionIfGenreDoesntExist(): void
    {
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => '?',
        ];

        $this->expectException(EntityNotFoundException::class);

        $this->movieService->create($data);
    }

    public function testUpdateUpdatesMovieIfDataIsValid(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => 'Science Fiction',
        ];

        $this->movieService->update($id, $data);

        $this->assertEquals('Avatar', $movie->getTitle());
        $this->assertEquals('Avatar is a 2009 science fiction film...', $movie->getDescription());
        $this->assertEquals(new DateTime('02:42:00'), $movie->getLength());
        $this->assertEquals(new DateTime('2009-12-25'), $movie->getReleaseDate());
    }


    public function testUpdateThrowsInvalidDataExceptionIfDataIsInvalid(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();
        $data = [
            'title' => 1,
            'release_date' => '2009-12-25',
        ];

        $this->expectException(InvalidDataException::class);

        $this->movieService->update($id, $data);
    }

    public function testUpdateThrowsEntityNotFoundExceptionIfGenreDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => '?',
        ];

        $this->expectException(EntityNotFoundException::class);

        $this->movieService->update($id, $data);
    }

    public function testUpdateThrowsEntityNotFoundExceptionIfMovieDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => 'Science Fiction',
        ];

        $this->expectException(EntityNotFoundException::class);

        $this->movieService->update($id, $data);
    }

    public function testDeleteDeletesMovieIfItExists(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();

        $this->movieService->delete($id);

        $this->assertCount(0, $this->movieRepository->findAll());
    }

    public function testDeleteThrowsEntityNotFoundExceptionIfMovieDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');

        $this->expectException(EntityNotFoundException::class);

        $this->movieService->delete($id);
    }
}
