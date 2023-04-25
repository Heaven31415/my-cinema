<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Controller\Api\V1;

use App\Factory\MovieFactory;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class MovieControllerTest extends WebTestCase
{
    protected KernelBrowser $client;
    protected MovieFactory $factory;
    protected MovieRepository $movieRepository;

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = $this->client->getContainer();

        $this->factory = $container->get(MovieFactory::class);
        $this->movieRepository = $container->get(MovieRepository::class);
    }

    public function testIndex_ReturnsOk(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->factory->create();
        }

        $this->client->jsonRequest('GET', 'api/v1/movies');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShow_ReturnsOk(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();

        $this->client->jsonRequest('GET', 'api/v1/movies/'.$id);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShow_ReturnsNotFound_IfMovieDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');

        $this->client->jsonRequest('GET', 'api/v1/movies/'.$id);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCreate_ReturnsCreated(): void
    {
        $this->client->jsonRequest('POST', 'api/v1/movies', [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => 'Science Fiction',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertCount(1, $this->movieRepository->findAll());
    }

    public function testCreate_ReturnsBadRequest_IfRequestBodyIsInvalid(): void
    {
        $this->client->jsonRequest('POST', 'api/v1/movies', [
            'title' => 1,
            'release_date' => '2009-12-25',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertCount(0, $this->movieRepository->findAll());
    }

    public function testCreate_ReturnsNotFound_IfGenreDoesntExist(): void
    {
        $this->client->jsonRequest('POST', 'api/v1/movies', [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => '?',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testUpdate_ReturnsNoContent(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();
        $this->client->jsonRequest('PUT', 'api/v1/movies/'.$id, [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => 'Science Fiction',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testUpdate_ReturnsBadRequest_IfRequestBodyIsInvalid(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();
        $this->client->jsonRequest('PUT', 'api/v1/movies/'.$id, [
            'title' => 1,
            'release_date' => '2009-12-25',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdate_ReturnsNotFound_IfGenreDoesntExist(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();
        $this->client->jsonRequest('PUT', 'api/v1/movies/'.$id, [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => '?',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testUpdate_ReturnsNotFound_IfMovieDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');
        $this->client->jsonRequest('PUT', 'api/v1/movies/'.$id, [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
            'genre' => 'Science Fiction',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDelete_ReturnsNoContent(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();

        $this->client->jsonRequest('DELETE', 'api/v1/movies/'.$id);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testDelete_ReturnsNotFound_IfMovieDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');

        $this->client->jsonRequest('DELETE', 'api/v1/movies/'.$id);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
