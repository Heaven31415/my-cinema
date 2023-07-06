<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Entity;

use App\Factory\MovieFactory;
use App\Factory\ShowFactory;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

class ShowTest extends KernelTestCase
{
    use Factories;

    protected ShowFactory $showFactory;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->showFactory = $container->get(ShowFactory::class);
    }

    public function testGetEnd_ReturnsProperDateTime(): void
    {
        $movie = MovieFactory::createOne(['durationInMinutes' => 60]);

        $show = $this->showFactory->create(
            ['movie' => $movie, 'startTime' => new DateTime('2020-09-28 12:00:00')]
        );

        $this->assertEquals(new DateTime('2020-09-28 13:00:00'), $show->getEndTime());
    }
}
