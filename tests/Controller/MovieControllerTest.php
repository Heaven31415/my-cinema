<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Controller;

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
    protected MovieRepository $repository;

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = $this->client->getContainer();

        $this->factory = $container->get(MovieFactory::class);
        $this->repository = $container->get(MovieRepository::class);
    }

    public function testIndexReturnsOk(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->factory->create();
        }

        $this->client->request('GET', '/movies');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowReturnsOk(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();

        $this->client->request('GET', "/movies/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowReturnsNotFoundIfMovieDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');

        $this->client->request('GET', "/movies/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCreateReturnsCreated(): void
    {
        $content = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
        ];

        $this->client->request('POST', '/movies', [], [], [], json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertCount(1, $this->repository->findAll());
    }

    public function testCreateReturnsBadRequestIfRequestBodyIsInvalid(): void
    {
        $content = [
            'title' => 1,
            'release_date' => '2009-12-25',
        ];

        $this->client->request('POST', '/movies', [], [], [], json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertCount(0, $this->repository->findAll());
    }

    public function testUpdateReturnsOk(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();
        $content = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
        ];

        $this->client->request('PUT', "/movies/$id", [], [], [], json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUpdateReturnsBadRequestIfRequestBodyIsInvalid(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();
        $content = [
            'title' => 1,
            'release_date' => '2009-12-25',
        ];

        $this->client->request('PUT', "/movies/$id", [], [], [], json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdateReturnsNotFoundIfMovieDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');
        $content = [
            'title' => 'Avatar',
            'description' => 'Avatar is a 2009 science fiction film...',
            'length' => '02:42:00',
            'release_date' => '2009-12-25',
        ];

        $this->client->request('PUT', "/movies/$id", [], [], [], json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteReturnsOk(): void
    {
        $movie = $this->factory->create();
        $id = $movie->getId();

        $this->client->request('DELETE', "/movies/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testDeleteReturnsNotFoundIfMovieDoesntExist(): void
    {
        $id = new Uuid('8a47fd24-34d3-4ed0-b69c-4d151bf277c6');

        $this->client->request('DELETE', "/movies/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
