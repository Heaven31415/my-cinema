<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Service;

use App\Factory\HallFactory;
use App\Factory\MovieFactory;
use App\Factory\ShowFactory;
use App\Service\ShowService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Zenstruck\Foundry\Test\Factories;

class ShowServiceTest extends WebTestCase
{
    use Factories;

    protected ShowService $showService;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->showService = $container->get(ShowService::class);
    }

    public function testFind_ReturnsShow_IfItExists(): void
    {
        $show = ShowFactory::createOne();
        $id = $show->getId();

        $foundShow = $this->showService->find($id);

        $this->assertEquals($show->object(), $foundShow);
    }

    public function testFind_ThrowsResourceNotFoundException_IfShowDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->showService->find($id);
    }

    public function testFindAll_ReturnsAllShows(): void
    {
        $shows = ShowFactory::createMany(2);

        $foundShows = $this->showService->findAll();

        $this->assertEquals($shows[0]->object(), $foundShows[0]);
        $this->assertEquals($shows[1]->object(), $foundShows[1]);
    }

    public function testCreate_CreatesShow_IfHallIsAvailable(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);
        $hall = HallFactory::createOne();

        $data = [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => '2020-09-28 12:00:00',
        ];

        $show = $this->showService->create($data);

        $this->assertEquals($movie->object(), $show->getMovie());
        $this->assertCount(1, $movie->getShows());

        $this->assertEquals($hall->object(), $show->getHall());
        $this->assertCount(1, $hall->getShows());

        $this->assertEquals(new DateTime('2020-09-28 12:00:00'), $show->getStartTime());
        $this->assertEquals(new DateTime('2020-09-28 13:00:00'), $show->getEndTime());
    }

    public function testCreate_ThrowsBadRequestException_IfHallIsNotAvailable(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);
        $hall = HallFactory::createOne();

        ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 12:00:00')]
        );

        $data = [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => '2020-09-28 12:30:00',
        ];

        $this->expectException(BadRequestHttpException::class);

        $this->showService->create($data);
    }

    public function testUpdate_UpdatesShow_IfHallIsAvailable(): void
    {
        $movieA = MovieFactory::createOne(['durationInMinutes' => 60]);
        $movieB = MovieFactory::createOne(['durationInMinutes' => 120]);

        $hallA = HallFactory::createOne();
        $hallB = HallFactory::createOne();

        $show = ShowFactory::createOne([
            'movie' => $movieA,
            'hall' => $hallA,
            'startTime' => new DateTime('2020-09-28 12:00:00'),
        ]);

        $id = $show->getId();
        $data = [
            'movie' => $movieB->getId(),
            'hall' => $hallB->getId(),
            'startTime' => '2020-09-28 13:00:00',
        ];

        $this->showService->update($id, $data);

        $this->assertCount(0, $movieA->getShows());
        $this->assertCount(1, $movieB->getShows());
        $this->assertEquals($movieB->object(), $show->getMovie());

        $this->assertCount(0, $hallA->getShows());
        $this->assertCount(1, $hallB->getShows());
        $this->assertEquals($hallB->object(), $show->getHall());

        $this->assertEquals(new DateTime('2020-09-28 13:00:00'), $show->getStartTime());
        $this->assertEquals(new DateTime('2020-09-28 15:00:00'), $show->getEndTime());
    }

    public function testUpdate_UpdatesShow_IfItMovesInsideItsTimeInterval(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);
        $hall = HallFactory::createOne();

        $show = ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 12:00:00')]
        );

        $id = $show->getId();
        $data = [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => '2020-09-28 12:30:00',
        ];

        $this->showService->update($id, $data);

        $this->assertEquals(new DateTime('2020-09-28 12:30:00'), $show->getStartTime());
        $this->assertEquals(new DateTime('2020-09-28 13:30:00'), $show->getEndTime());
    }

    public function testUpdate_ThrowsBadRequestException_IfHallIsNotAvailable(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);
        $hall = HallFactory::createOne();

        ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 12:00:00')]
        );

        $show = ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 14:00:00')]
        );

        $id = $show->getId();
        $data = [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => '2020-09-28 12:00:00',
        ];

        $this->expectException(BadRequestHttpException::class);

        $this->showService->update($id, $data);
    }

    public function testUpdate_ThrowsResourceNotFoundException_IfShowDoesntExist(): void
    {
        $movie = MovieFactory::createOne();
        $hall = HallFactory::createOne();

        $id = 0;
        $data = [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => '2020-09-28 12:00:00',
        ];

        $this->expectException(ResourceNotFoundException::class);

        $this->showService->update($id, $data);
    }

    public function testDelete_DeletesShow_IfItExists(): void
    {
        $show = ShowFactory::createOne();
        $id = $show->getId();

        $this->showService->delete($id);

        ShowFactory::assert()->empty();
    }

    public function testDelete_ThrowsResourceNotFoundException_IfShowDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->showService->delete($id);
    }
}
