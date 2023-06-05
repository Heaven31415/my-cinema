<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Entity;

use App\Factory\MovieFactory;
use App\Factory\ShowFactory;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShowTest extends KernelTestCase
{
    protected MovieFactory $movieFactory;
    protected ShowFactory $showFactory;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->movieFactory = $container->get(MovieFactory::class);
        $this->showFactory = $container->get(ShowFactory::class);
    }

    public function testGetEnd_ReturnsProperDateTime(): void
    {
        $movie = $this->movieFactory->create(['durationInMinutes' => 60]);

        $show = $this->showFactory->create(
            ['movie' => $movie, 'start' => new DateTime('2020-09-28 12:00:00')]
        );

        $this->assertEquals(new DateTime('2020-09-28 13:00:00'), $show->getEnd());
    }
}
