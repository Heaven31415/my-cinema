<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Service;

use App\Factory\HallFactory;
use App\Repository\HallRepository;
use App\Service\HallService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class HallServiceTest extends WebTestCase
{
    protected HallFactory $factory;
    protected HallRepository $hallRepository;
    protected HallService $hallService;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->factory = $container->get(HallFactory::class);
        $this->hallRepository = $container->get(HallRepository::class);
        $this->hallService = $container->get(HallService::class);
    }

    public function testFind_ReturnsHall_IfItExists(): void
    {
        $hall = $this->factory->create();
        $id = $hall->getId();

        $foundHall = $this->hallService->find($id);

        $this->assertEquals($hall, $foundHall);
    }

    public function testFind_ThrowsResourceNotFoundException_IfHallDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->hallService->find($id);
    }

    public function testFindAll_ReturnsAllHalls(): void
    {
        $halls = [$this->factory->create(), $this->factory->create()];

        $foundHalls = $this->hallService->findAll();

        $this->assertEquals($halls, $foundHalls);
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
        $hall = $this->factory->create();
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
        $hall = $this->factory->create();
        $id = $hall->getId();

        $this->hallService->delete($id);

        $this->assertCount(0, $this->hallRepository->findAll());
    }

    public function testDelete_ThrowsResourceNotFoundException_IfHallDoesntExist(): void
    {
        $id = 0;

        $this->expectException(ResourceNotFoundException::class);

        $this->hallService->delete($id);
    }
}
