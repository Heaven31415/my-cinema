<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Service;

use App\Factory\GenreFactory;
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

    public function testFindAll_ReturnsShowsSortedByStartTimeInAscendingOrder(): void
    {
        ShowFactory::createSequence(function () {
            foreach (range(3, 1) as $day) {
                yield ['startTime' => new DateTime('2020-09-0'.$day.' 12:00:00')];
            }
        });

        ShowFactory::createSequence(function () {
            foreach (range(15, 12) as $hour) {
                yield ['startTime' => new DateTime('2020-09-04 '.$hour.':00:00')];
            }
        });

        $foundShows = $this->showService->findAll();

        $this->assertEquals('2020-09-01', $foundShows[0]->getStartTime()->format('Y-m-d'));
        $this->assertEquals('2020-09-02', $foundShows[1]->getStartTime()->format('Y-m-d'));
        $this->assertEquals('2020-09-03', $foundShows[2]->getStartTime()->format('Y-m-d'));

        $this->assertEquals('12:00:00', $foundShows[3]->getStartTime()->format('H:i:s'));
        $this->assertEquals('13:00:00', $foundShows[4]->getStartTime()->format('H:i:s'));
        $this->assertEquals('14:00:00', $foundShows[5]->getStartTime()->format('H:i:s'));
    }

    public function testFindAll_ReturnsAllShows_IfNoFiltersAreProvided(): void
    {
        $shows = ShowFactory::createMany(2);

        $foundShows = $this->showService->findAll();

        $this->assertCount(2, $foundShows);
        $this->assertContains($shows[0]->object(), $foundShows);
        $this->assertContains($shows[1]->object(), $foundShows);
    }

    public function testFindAll_ReturnsShowsWithProperMovieTitle_IfTitleFilterIsProvided(): void
    {
        ShowFactory::createSequence([
            ['movie' => MovieFactory::new(['title' => 'Avatar'])],
            ['movie' => MovieFactory::new(['title' => 'Titanic'])],
        ]);

        $foundShows = $this->showService->findAll('Avatar');

        $this->assertCount(1, $foundShows);
        $this->assertEquals('Avatar', $foundShows[0]->getMovie()->getTitle());
    }

    public function testFindAll_ReturnsShowsWithProperMovieGenre_IfGenreFilterIsProvided(): void
    {
        ShowFactory::createSequence([
            ['movie' => MovieFactory::new(['genre' => GenreFactory::find(['name' => 'Action'])])],
            ['movie' => MovieFactory::new(['genre' => GenreFactory::find(['name' => 'Comedy'])])],
        ]);

        $foundShows = $this->showService->findAll(null, 'Action');

        $this->assertCount(1, $foundShows);
        $this->assertEquals('Action', $foundShows[0]->getMovie()->getGenre()->getName());
    }

    public function testFindAll_ReturnsShowsPlayedAfterProvidedDate_IfFromFilterIsProvided(): void
    {
        ShowFactory::createSequence([
            ['startTime' => new DateTime('2020-09-27 12:00:00')],
            ['startTime' => new DateTime('2020-09-28 12:00:00')],
        ]);

        $foundShows = $this->showService->findAll(null, null, '2020-09-28');

        $this->assertCount(1, $foundShows);
        $this->assertEquals('2020-09-28', $foundShows[0]->getStartTime()->format('Y-m-d'));
    }

    public function testFindAll_ReturnsShowsPlayedBeforeProvidedDate_IfToFilterIsProvided(): void
    {
        ShowFactory::createSequence([
            ['startTime' => new DateTime('2020-09-27 12:00:00')],
            ['startTime' => new DateTime('2020-09-28 12:00:00')],
        ]);

        $foundShows = $this->showService->findAll(null, null, null, '2020-09-28');

        $this->assertCount(1, $foundShows);
        $this->assertEquals('2020-09-27', $foundShows[0]->getStartTime()->format('Y-m-d'));
    }

    public function testFindAll_ReturnsValidShowsSubset_IfAllFiltersAreProvided(): void
    {
        ShowFactory::createSequence([
            [
                'startTime' => new DateTime('2020-09-28 12:00:00'),
                'movie' => MovieFactory::new(
                    ['title' => 'Avatar', 'genre' => GenreFactory::find(['name' => 'Action'])]
                ),
            ],
            ['startTime' => new DateTime('2020-09-27')],
            ['startTime' => new DateTime('2020-09-29')],
            ['movie' => MovieFactory::new(['title' => 'Titanic'])],
            ['movie' => MovieFactory::new(['genre' => GenreFactory::find(['name' => 'Comedy'])])],
        ]);

        $foundShows = $this->showService->findAll('Avatar', 'Action', '2020-09-28', '2020-09-29');

        $this->assertCount(1, $foundShows);
        $this->assertEquals('Avatar', $foundShows[0]->getMovie()->getTitle());
        $this->assertEquals('Action', $foundShows[0]->getMovie()->getGenre()->getName());
        $this->assertEquals('2020-09-28', $foundShows[0]->getStartTime()->format('Y-m-d'));
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
