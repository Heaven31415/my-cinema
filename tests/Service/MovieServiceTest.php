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

    public function testFind_ReturnsMovie_IfItExists(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();

        $foundMovie = $this->movieService->find($id);

        $this->assertEquals($movie, $foundMovie);
    }

    public function testFind_ThrowsResourceNotFoundException_IfMovieDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');

        $this->expectException(ResourceNotFoundException::class);

        $this->movieService->find($id);
    }

    public function testFindAll_ReturnsAllMovies(): void
    {
        $movies = [$this->factory->create(), $this->factory->create()];

        $foundMovies = $this->movieService->findAll();

        $this->assertEquals($movies, $foundMovies);
    }

    public function testCreate_CreatesMovie(): void
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
        $this->assertEquals('Science Fiction', $movie->getGenre()->getName());
        $this->assertCount(1, $this->movieRepository->findAll());
    }

    public function testCreate_ThrowsResourceNotFoundException_IfGenreDoesntExist(): void
    {
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => '?',
        ];

        $this->expectException(ResourceNotFoundException::class);

        $this->movieService->create($data);
    }

    public function testUpdate_UpdatesMovie(): void
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
        $this->assertEquals('Science Fiction', $movie->getGenre()->getName());
    }


    public function testUpdate_ThrowsResourceNotFoundException_IfGenreDoesntExist(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => '?',
        ];

        $this->expectException(ResourceNotFoundException::class);

        $this->movieService->update($id, $data);
    }

    public function testUpdate_ThrowsResourceNotFoundException_IfMovieDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');
        $data = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => 'Science Fiction',
        ];

        $this->expectException(ResourceNotFoundException::class);

        $this->movieService->update($id, $data);
    }

    public function testDelete_DeletesMovie_IfItExists(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();

        $this->movieService->delete($id);

        $this->assertCount(0, $this->movieRepository->findAll());
    }

    public function testDelete_ThrowsResourceNotFoundException_IfMovieDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');

        $this->expectException(ResourceNotFoundException::class);

        $this->movieService->delete($id);
    }
}
