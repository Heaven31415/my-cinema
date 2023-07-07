<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Service;

use App\Factory\HallFactory;
use App\Service\HallService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Zenstruck\Foundry\Test\Factories;

class HallServiceTest extends WebTestCase
{
    use Factories;

    protected HallService $hallService;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->hallService = $container->get(HallService::class);
    }

    public function testFind_ReturnsHall_IfItExists(): void
    {
        $hall = HallFactory::createOne();
        $id = $hall->getId();

        $foundHall = $this->hallService->find($id);

        $this->assertEquals($hall->object(), $foundHall);
    }

    public function testFind_ThrowsResourceNotFoundException_IfHallDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->hallService->find($id);
    }

    public function testFindAll_ReturnsAllHalls(): void
    {
        $halls = HallFactory::createMany(2);

        $foundHalls = $this->hallService->findAll();

        $this->assertEquals($halls[0]->object(), $foundHalls[0]);
        $this->assertEquals($halls[1]->object(), $foundHalls[1]);
    }

    public function testCreate_CreatesHall(): void
    {
        $data = [
            'name' => 'A1',
            'capacity' => 1,
        ];

        $hall = $this->hallService->create($data);

        $this->assertEquals('A1', $hall->getName());
        $this->assertEquals(1, $hall->getCapacity());
    }

    public function testUpdate_UpdatesHall(): void
    {
        $hall = HallFactory::createOne();
        $id = $hall->getId();
        $data = [
            'name' => 'A1',
            'capacity' => 1,
        ];

        $this->hallService->update($id, $data);

        $this->assertEquals('A1', $hall->getName());
        $this->assertEquals(1, $hall->getCapacity());
    }

    public function testUpdate_ThrowsResourceNotFoundException_IfHallDoesntExist(): void
    {
        $id = 0;
        $data = [
            'name' => 'A1',
            'capacity' => 1,
        ];

        $this->expectException(ResourceNotFoundException::class);

        $this->hallService->update($id, $data);
    }

    public function testDelete_DeletesHall_IfItExists(): void
    {
        $hall = HallFactory::createOne();
        $id = $hall->getId();

        $this->hallService->delete($id);

        HallFactory::assert()->empty();
    }

    public function testDelete_ThrowsResourceNotFoundException_IfHallDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->hallService->delete($id);
    }
}
