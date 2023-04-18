<?php

namespace App\Tests\Controller;

use App\Factory\HallFactory;
use App\Repository\HallRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HallControllerTest extends WebTestCase
{
    protected KernelBrowser $client;
    protected HallFactory $factory;
    protected HallRepository $hallRepository;

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = $this->client->getContainer();

        $this->factory = $container->get(HallFactory::class);
        $this->hallRepository = $container->get(HAllRepository::class);
    }

    public function testIndexReturnsOk(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->factory->create();
        }

        $this->client->request('GET', '/halls');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowReturnsOk(): void
    {
        $hall = $this->factory->create();
        $id = $hall->getId();

        $this->client->request('GET', "/halls/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowReturnsNotFoundIfHallDoesntExist(): void
    {
        $id = 0;

        $this->client->request('GET', "/halls/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCreateReturnsCreated(): void
    {
        $content = [
            'name' => 'A1',
            'capacity' => 1,
        ];

        $this->client->request('POST', '/halls', [], [], [], json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertCount(1, $this->hallRepository->findAll());
    }

    public function testCreateReturnsBadRequestIfRequestBodyIsInvalid(): void
    {
        $content = [
            'name' => 'A1',
            'capacity' => 0,
        ];

        $this->client->request('POST', '/halls', [], [], [], json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertCount(0, $this->hallRepository->findAll());
    }

    public function testUpdateReturnsOk(): void
    {
        $hall = $this->factory->create();
        $id = $hall->getId();
        $content = [
            'name' => 'A1',
            'capacity' => 1
        ];

        $this->client->request('PUT', "/halls/$id", [], [], [], json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUpdateReturnsBadRequestIfRequestBodyIsInvalid(): void
    {
        $hall = $this->factory->create();
        $id = $hall->getId();
        $content = [
            'name' => 'A1',
            'capacity' => 0,
        ];

        $this->client->request('PUT', "/halls/$id", [], [], [], json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdateReturnsNotFoundIfHallDoesntExist(): void
    {
        $id = 0;
        $content = [
            'name' => 'A1',
            'capacity' => 1,
        ];

        $this->client->request('PUT', "/halls/$id", [], [], [], json_encode($content));

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteReturnsOk(): void
    {
        $hall = $this->factory->create();
        $id = $hall->getId();

        $this->client->request('DELETE', "/halls/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testDeleteReturnsNotFoundIfHallDoesntExist(): void
    {
        $id = 0;

        $this->client->request('DELETE', "/halls/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
