<?php

namespace App\Tests\Controller\Api\V1;

use App\Factory\HallFactory;
use FOS\RestBundle\Exception\InvalidParameterException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Zenstruck\Foundry\Test\Factories;

class HallControllerTest extends WebTestCase
{
    use Factories;

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);
    }

    public function testIndex_ReturnsOk(): void
    {
        HallFactory::createMany(2);

        $this->client->jsonRequest('GET', '/api/v1/halls');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShow_ReturnsOk(): void
    {
        $hall = HallFactory::createOne();
        $id = $hall->getId();

        $this->client->jsonRequest('GET', "api/v1/halls/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShow_ReturnsNotFound_IfHallDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

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
        HallFactory::assert()->count(1);
    }

    public function testCreate_ReturnsBadRequest_IfRequestBodyIsInvalid(): void
    {
        $this->expectException(InvalidParameterException::class);

        $this->client->jsonRequest('POST', 'api/v1/halls', [
            'name' => 'A1',
            'capacity' => 0,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        HallFactory::assert()->empty();
    }

    public function testUpdate_ReturnsNoContent(): void
    {
        $hall = HallFactory::createOne();
        $id = $hall->getId();

        $this->client->jsonRequest('PUT', "api/v1/halls/$id", [
            'name' => 'A1',
            'capacity' => 1,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testUpdate_ReturnsBadRequest_IfRequestBodyIsInvalid(): void
    {
        $hall = HallFactory::createOne();
        $id = $hall->getId();

        $this->expectException(InvalidParameterException::class);

        $this->client->jsonRequest('PUT', "api/v1/halls/$id", [
            'name' => 'A1',
            'capacity' => 0,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdate_ReturnsNotFound_IfHallDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->client->jsonRequest('PUT', "api/v1/halls/$id", [
            'name' => 'A1',
            'capacity' => 1,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testDelete_ReturnsNoContent(): void
    {
        $hall = HallFactory::createOne();
        $id = $hall->getId();

        $this->client->jsonRequest('DELETE', "api/v1/halls/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testDelete_ReturnsNotFound_IfHallDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->client->jsonRequest('DELETE', "api/v1/halls/$id");

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
