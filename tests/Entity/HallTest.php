<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Entity;

use App\Factory\HallFactory;
use App\Factory\MovieFactory;
use App\Factory\ShowFactory;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HallTest extends KernelTestCase
{
    protected MovieFactory $movieFactory;
    protected HallFactory $hallFactory;
    protected ShowFactory $showFactory;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->movieFactory = $container->get(MovieFactory::class);
        $this->hallFactory = $container->get(HallFactory::class);
        $this->showFactory = $container->get(ShowFactory::class);
    }

    public function testGetShowsForTimeInterval_ReturnsShows_IfTheyAreInsideInterval(): void
    {
        $movie = $this->movieFactory->create(['durationInMinutes' => 60]);
        $hall = $this->hallFactory->create();

        $showA = $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'start' => new DateTime('2020-09-28 18:30:00')]
        );

        $showB = $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'start' => new DateTime('2020-09-28 20:00:00')]
        );

        $showC = $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'start' => new DateTime('2020-09-28 21:30:00')]
        );

        $from = new DateTime('2020-09-28 19:00:00');
        $to = new DateTime('2020-09-28 22:00:00');

        $shows = $hall->getShowsForTimeInterval($from, $to);

        $this->assertCount(3, $shows);

        $this->assertContains($showA, $shows);
        $this->assertContains($showB, $shows);
        $this->assertContains($showC, $shows);
    }

    public function testGetShowsForTimeInterval_DoesntReturnShows_IfTheyAreNotInsideInterval(): void
    {
        $movie = $this->movieFactory->create(['durationInMinutes' => 60]);
        $hall = $this->hallFactory->create();

        $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'start' => new DateTime('2020-09-28 17:00:00')]
        );

        $this->showFactory->create(
            ['movie' => $movie, 'hall' => $hall, 'start' => new DateTime('2020-09-28 23:00:00')]
        );

        $from = new DateTime('2020-09-28 19:00:00');
        $to = new DateTime('2020-09-28 22:00:00');

        $this->assertCount(0, $hall->getShowsForTimeInterval($from, $to));
    }
}
