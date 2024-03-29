<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Controller\Api\V1;

use App\Factory\HallFactory;
use App\Factory\MovieFactory;
use App\Factory\ShowFactory;
use App\Service\ShowService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Zenstruck\Foundry\Test\Factories;

class ShowControllerTest extends WebTestCase
{
    use Factories;

    protected KernelBrowser $client;
    protected final const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);
    }

    public function testIndex_ReturnsProperResponseStructure(): void
    {
        $showA = ShowFactory::createOne(['startTime' => new DateTime('2020-09-28 12:00:00')]);
        $showB = ShowFactory::createOne(['startTime' => new DateTime('2020-09-28 13:00:00')]);

        $this->client->jsonRequest('GET', 'api/v1/shows');
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(2, $response);

        $this->assertEquals($showA->getId(), $response[0]['id']);
        $this->assertEquals($showA->getMovie()->getId(), $response[0]['movie']['id']);
        $this->assertEquals($showA->getHall()->getId(), $response[0]['hall']['id']);
        $this->assertEquals(
            $showA->getStartTime()->format(self::DATE_TIME_FORMAT),
            $response[0]['startTime']
        );

        $this->assertEquals($showB->getId(), $response[1]['id']);
        $this->assertEquals($showB->getMovie()->getId(), $response[1]['movie']['id']);
        $this->assertEquals($showB->getHall()->getId(), $response[1]['hall']['id']);
        $this->assertEquals(
            $showB->getStartTime()->format(self::DATE_TIME_FORMAT),
            $response[1]['startTime']
        );
    }

    public function testIndex_CallsFindAllWithoutArguments_IfNoFiltersAreProvided(): void
    {
        $showService = $this->createMock(ShowService::class);
        $showService->expects(self::once())
            ->method('findAll')
            ->with(null, null, null, null);
        $this->getContainer()->set(ShowService::class, $showService);

        $this->client->jsonRequest('GET', 'api/v1/shows');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testIndex_CallsFindAllWithArguments_IfFiltersAreProvided(): void
    {
        $showService = $this->createMock(ShowService::class);
        $showService->expects(self::once())
            ->method('findAll')
            ->with('Avatar', 'Action', null, null);
        $this->getContainer()->set(ShowService::class, $showService);

        $this->client->jsonRequest('GET', 'api/v1/shows?title=Avatar&genre=Action');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShow_ReturnsShow(): void
    {
        $show = ShowFactory::createOne();
        $id = $show->getId();

        $this->client->jsonRequest('GET', 'api/v1/shows/'.$id);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertEquals($show->getId(), $response['id']);
        $this->assertEquals($show->getMovie()->getId(), $response['movie']['id']);
        $this->assertEquals($show->getHall()->getId(), $response['hall']['id']);
        $this->assertEquals(
            $show->getStartTime()->format(self::DATE_TIME_FORMAT),
            $response['startTime']
        );
    }

    public function testShow_ThrowsResourceNotFoundException_IfShowDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->client->jsonRequest('GET', 'api/v1/shows/'.$id);
    }

    public function testCreate_CreatesShow(): void
    {
        $movie = MovieFactory::createOne();
        $hall = HallFactory::createOne();
        $startTime = '2020-09-28 12:00:00';

        $this->client->jsonRequest('POST', 'api/v1/shows', [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => $startTime,
        ]);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->assertTrue(is_int($response['id']));
        $this->assertEquals($movie->getId(), $response['movie']['id']);
        $this->assertEquals($hall->getId(), $response['hall']['id']);
        $this->assertEquals($startTime, $response['startTime']);

        ShowFactory::assert()->count(1);
    }

    public function testCreate_ThrowsBadRequestHttpException_IfHallIsNotAvailable(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 180]);
        $hall = HallFactory::createOne(['name' => 'A1']);

        ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 19:00:00')]
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Hall "A1" is not available during that time');

        $this->client->jsonRequest('POST', 'api/v1/shows', [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => '2020-09-28 20:00:00',
        ]);
    }

    public function testCreate_ThrowsBadRequestHttpException_IfRequestIsInvalid(): void
    {
        $movie = MovieFactory::createOne();
        $hall = HallFactory::createOne();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage(
            'Parameter "startTime" of value "there is no time" violated a constraint'
        );

        $this->client->jsonRequest('POST', 'api/v1/shows', [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => 'there is no time',
        ]);
    }

    public function testUpdate_UpdatesShow(): void
    {
        $movie = MovieFactory::createOne();
        $hall = HallFactory::createOne();
        $startTime = '2020-09-28 12:00:00';

        $show = ShowFactory::createOne();
        $id = $show->getId();

        $this->client->jsonRequest('PUT', 'api/v1/shows/'.$id, [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => $startTime,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->assertTrue($this->client->getResponse()->isEmpty());

        $this->assertEquals($movie->object(), $show->getMovie());
        $this->assertEquals($hall->object(), $show->getHall());
        $this->assertEquals($startTime, $show->getStartTime()->format(self::DATE_TIME_FORMAT));
    }

    public function testUpdate_ThrowsBadRequestHttpException_IfHallIsNotAvailable(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);
        $hall = HallFactory::createOne(['name' => 'A1']);

        ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 19:00:00')]
        );
        $show = ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 20:00:00')]
        );
        $id = $show->getId();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Hall "A1" is not available during that time');

        $this->client->jsonRequest('PUT', 'api/v1/shows/'.$id, [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => '2020-09-28 19:00:00',
        ]);
    }

    public function testUpdate_ThrowsBadRequestHttpException_IfRequestIsInvalid(): void
    {
        $movie = MovieFactory::createOne();
        $hall = HallFactory::createOne();
        $show = ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 19:00:00')]
        );
        $id = $show->getId();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage(
            'Parameter "startTime" of value "there is no time" violated a constraint'
        );

        $this->client->jsonRequest('PUT', 'api/v1/shows/'.$id, [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => 'there is no time',
        ]);
    }

    public function testUpdate_ThrowsResourceNotFoundException_IfShowDoesntExist(): void
    {
        $movie = MovieFactory::createOne();
        $hall = HallFactory::createOne();
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->client->jsonRequest('PUT', 'api/v1/shows/'.$id, [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => '2020-09-28 12:00:00',
        ]);
    }

    public function testDelete_DeletesShow(): void
    {
        $show = ShowFactory::createOne();
        $id = $show->getId();

        $this->client->jsonRequest('DELETE', 'api/v1/shows/'.$id);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->assertTrue($this->client->getResponse()->isEmpty());

        ShowFactory::assert()->empty();
    }

    public function testDelete_ThrowsResourceNotFoundException_IfShowDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->client->jsonRequest('DELETE', 'api/v1/shows/'.$id);
    }
}