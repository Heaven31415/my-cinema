<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Service;

use App\Factory\HallFactory;
use App\Factory\MovieFactory;
use App\Factory\ShowFactory;
use App\Repository\ShowRepository;
use App\Service\ShowService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ShowServiceTest extends WebTestCase
{
    protected MovieFactory $movieFactory;
    protected HallFactory $hallFactory;
    protected ShowFactory $showFactory;
    protected ShowRepository $showRepository;
    protected ShowService $showService;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->movieFactory = $container->get(MovieFactory::class);
        $this->hallFactory = $container->get(HallFactory::class);
        $this->showFactory = $container->get(ShowFactory::class);
        $this->showRepository = $container->get(ShowRepository::class);
        $this->showService = $container->get(ShowService::class);
    }

    public function testFind_ReturnsShow_IfItExists(): void
    {
        $show = $this->showFactory->create();
        $id = $show->getId();

        $foundShow = $this->showService->find($id);

        $this->assertEquals($show, $foundShow);
    }

    public function testFind_ThrowsResourceNotFoundException_IfShowDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->showService->find($id);
    }

    public function testFindAll_ReturnsAllShows(): void
    {
        $shows = [$this->showFactory->create(), $this->showFactory->create()];

        $foundShows = $this->showService->findAll();

        $this->assertEquals($shows, $foundShows);
    }

    public function testCreate_CreatesShow_IfHallIsNotBusy(): void
    {
        $movie = $this->movieFactory->create(['durationInMinutes' => 60]);
        $hall = $this->hallFactory->create();

        $data = [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => '2020-09-28 12:00:00',
        ];

        $show = $this->showService->create($data);

        $this->assertEquals($movie, $show->getMovie());
        $this->assertCount(1, $movie->getShows());

        $this->assertEquals($hall, $show->getHall());
        $this->assertCount(1, $hall->getShows());

        $this->assertEquals(new DateTime('2020-09-28 12:00:00'), $show->getStartTime());
        $this->assertEquals(new DateTime('2020-09-28 13:00:00'), $show->getEndTime());
    }

    public function testCreate_ThrowsBadRequestException_IfHallIsBusy(): void
    {
        $movie = $this->movieFactory->create(['durationInMinutes' => 60]);
        $hall = $this->hallFactory->create();

        $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 12:00:00')]
        );

        $data = [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => '2020-09-28 12:30:00',
        ];

        $this->expectException(BadRequestException::class);

        $this->showService->create($data);

        $this->assertCount(0, $this->showRepository->findAll());
    }

    public function testUpdate_UpdatesShow_IfHallIsNotBusy(): void
    {
        $movieA = $this->movieFactory->create(['durationInMinutes' => 60]);
        $movieB = $this->movieFactory->create(['durationInMinutes' => 120]);

        $hallA = $this->hallFactory->create();
        $hallB = $this->hallFactory->create();

        $show = $this->showFactory->create(
            ['movie' => $movieA, 'hall' => $hallA, 'startTime' => new DateTime('2020-09-28 12:00:00')]
        );

        $id = $show->getId();
        $data = [
            'movie' => $movieB->getId(),
            'hall' => $hallB->getId(),
            'startTime' => '2020-09-28 13:00:00',
        ];

        $this->showService->update($id, $data);

        $this->assertCount(0, $movieA->getShows());
        $this->assertEquals($movieB, $show->getMovie());
        $this->assertCount(1, $movieB->getShows());

        $this->assertCount(0, $hallA->getShows());
        $this->assertEquals($hallB, $show->getHall());
        $this->assertCount(1, $hallB->getShows());

        $this->assertEquals(new DateTime('2020-09-28 13:00:00'), $show->getStartTime());
        $this->assertEquals(new DateTime('2020-09-28 15:00:00'), $show->getEndTime());
    }

    public function testUpdate_ThrowsBadRequestException_IfHallIsBusy(): void
    {
        $movie = $this->movieFactory->create(['durationInMinutes' => 60]);
        $hall = $this->hallFactory->create();

        $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 12:00:00')]
        );

        $show = $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 14:00:00')]
        );

        $id = $show->getId();
        $data = [
            'movie' => $movie->getId(),
            'hall' => $hall->getId(),
            'startTime' => '2020-09-28 12:00:00',
        ];

        $this->expectException(BadRequestException::class);

        $this->showService->update($id, $data);
    }

    public function testUpdate_ThrowsResourceNotFoundException_IfShowDoesntExist(): void
    {
        $movie = $this->movieFactory->create(['durationInMinutes' => 60]);
        $hall = $this->hallFactory->create();

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
        $show = $this->showFactory->create();
        $id = $show->getId();

        $this->showService->delete($id);

        $this->assertCount(0, $this->showRepository->findAll());
    }

    public function testDelete_ThrowsResourceNotFoundException_IfShowDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->showService->delete($id);
    }
}
