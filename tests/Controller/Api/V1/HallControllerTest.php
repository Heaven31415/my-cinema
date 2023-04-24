<?php

namespace App\Tests\Controller\Api\V1;

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

    public function testIndex_ReturnsOk(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->factory->create();
        }

        $this->client->jsonRequest('GET', '/api/v1/halls');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShow_ReturnsOk(): void
    {
        $hall = $this->factory->create();
        $id = $hall->getId();

        $this->client->jsonRequest('GET', "api/v1/halls/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShow_ReturnsNotFound_IfHallDoesntExist(): void
    {
        $id = 0;

        $this->client->jsonRequest('GET', "api/v1/halls/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCreate_ReturnsCreated(): void
    {
        $this->client->jsonRequest('POST', 'api/v1/halls', [
            'name' => 'A1',
            'capacity' => 1,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertCount(1, $this->hallRepository->findAll());
    }

    public function testCreate_ReturnsBadRequest_IfRequestBodyIsInvalid(): void
    {
        $this->client->jsonRequest('POST', 'api/v1/halls', [
            'name' => 'A1',
            'capacity' => 0,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertCount(0, $this->hallRepository->findAll());
    }

    public function testUpdate_ReturnsOk(): void
    {
        $hall = $this->factory->create();
        $id = $hall->getId();

        $this->client->jsonRequest('PUT', "api/v1/halls/$id", [
            'name' => 'A1',
            'capacity' => 1,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testUpdate_ReturnsBadRequest_IfRequestBodyIsInvalid(): void
    {
        $hall = $this->factory->create();
        $id = $hall->getId();

        $this->client->jsonRequest('PUT', "api/v1/halls/$id", [
            'name' => 'A1',
            'capacity' => 0,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdate_ReturnsNotFound_IfHallDoesntExist(): void
    {
        $id = 0;

        $this->client->jsonRequest('PUT', "api/v1/halls/$id", [
            'name' => 'A1',
            'capacity' => 1,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDelete_ReturnsOk(): void
    {
        $hall = $this->factory->create();
        $id = $hall->getId();

        $this->client->jsonRequest('DELETE', "api/v1/halls/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testDelete_ReturnsNotFound_IfHallDoesntExist(): void
    {
        $id = 0;

        $this->client->jsonRequest('DELETE', "api/v1/halls/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
