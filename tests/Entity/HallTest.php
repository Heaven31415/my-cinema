<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Entity;

use App\Factory\HallFactory;
use App\Factory\MovieFactory;
use App\Factory\ShowFactory;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

class HallTest extends KernelTestCase
{
    use Factories;

    protected ShowFactory $showFactory;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->showFactory = $container->get(ShowFactory::class);
    }

    public function testGetShowsForTimeInterval_ReturnsShows_IfTheyAreInsideInterval(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);
        $hall = HallFactory::createOne();

        $showA = $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 18:30:00')]
        );

        $showB = $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 20:00:00')]
        );

        $showC = $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 21:30:00')]
        );

        $showD = $this->showFactory->create(
            [
                'movie' => MovieFactory::createOne(['durationInMinutes' => 180]),
                'hall' => $hall,
                'startTime' => new DateTime('2020-09-28 19:00:00'),
            ]
        );

        $from = new DateTime('2020-09-28 19:00:00');
        $to = new DateTime('2020-09-28 22:00:00');

        $shows = $hall->getShowsForTimeInterval($from, $to);

        $this->assertCount(4, $shows);

        $this->assertContains($showA, $shows);
        $this->assertContains($showB, $shows);
        $this->assertContains($showC, $shows);
        $this->assertContains($showD, $shows);
    }

    public function testGetShowsForTimeInterval_DoesntReturnShows_IfTheyAreNotInsideInterval(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);
        $hall = HallFactory::createOne();

        $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 17:00:00')]
        );

        $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 23:00:00')]
        );

        $from = new DateTime('2020-09-28 19:00:00');
        $to = new DateTime('2020-09-28 22:00:00');

        $this->assertCount(0, $hall->getShowsForTimeInterval($from, $to));
    }

    public function testGetShowsForTimeInterval_DoesntReturnShow_IfItIsExcluded(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);
        $hall = HallFactory::createOne();

        $show = $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 12:00:00')]
        );

        $from = new DateTime('2020-09-28 12:00:00');
        $to = new DateTime('2020-09-28 13:00:00');

        $this->assertCount(0, $hall->getShowsForTimeInterval($from, $to, $show));
    }

    public function testCanPlayMovie_ReturnsTrue_IfThereIsEnoughTimeToPlayMovie(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);
        $hall = HallFactory::createOne();

        $result = $hall->canPlayMovie(new DateTime('2020-09-28 12:00:00'), $movie->object());

        $this->assertTrue($result);
    }

    public function testCanPlayMovie_ReturnsFalse_IfThereIsNotEnoughTimeToPlayMovie(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);
        $hall = HallFactory::createOne();

        $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 11:30:00')]
        );

        $result = $hall->canPlayMovie(new DateTime('2020-09-28 12:00:00'), $movie->object());

        $this->assertFalse($result);
    }
}
