<?php

namespace App\Tests\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class GenreControllerTest extends WebTestCase
{
    use Factories;

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndex_ReturnsOk(): void
    {
        $this->client->jsonRequest('GET', '/api/v1/genres');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
