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

    public function testGetShowsForTimeInterval_ReturnsShows_IfTheyAreInsideInterval(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);
        $hall = HallFactory::createOne();

        $showA = ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 18:30:00')]
        );

        $showB = ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 20:00:00')]
        );

        $showC = ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 21:30:00')]
        );

        $showD = ShowFactory::createOne([
            'movie' => MovieFactory::createOne(['durationInMinutes' => 180]),
            'hall' => $hall,
            'startTime' => new DateTime('2020-09-28 19:00:00'),
        ]);

        $from = new DateTime('2020-09-28 19:00:00');
        $to = new DateTime('2020-09-28 22:00:00');

        $shows = $hall->getShowsForTimeInterval($from, $to);

        $this->assertCount(4, $shows);

        $this->assertContains($showA->object(), $shows);
        $this->assertContains($showB->object(), $shows);
        $this->assertContains($showC->object(), $shows);
        $this->assertContains($showD->object(), $shows);
    }

    public function testGetShowsForTimeInterval_DoesntReturnShows_IfTheyAreNotInsideInterval(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);
        $hall = HallFactory::createOne();

        ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 17:00:00')]
        );

        ShowFactory::createOne(
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

        $show = ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 12:00:00')]
        );

        $from = new DateTime('2020-09-28 12:00:00');
        $to = new DateTime('2020-09-28 13:00:00');

        $this->assertCount(0, $hall->getShowsForTimeInterval($from, $to, $show->object()));
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

        ShowFactory::createOne(
            ['movie' => $movie, 'hall' => $hall, 'startTime' => new DateTime('2020-09-28 11:30:00')]
        );

        $result = $hall->canPlayMovie(new DateTime('2020-09-28 12:00:00'), $movie->object());

        $this->assertFalse($result);
    }
}
