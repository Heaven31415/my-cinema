<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Service;

use App\Exception\EntityNotFoundException;
use App\Exception\InvalidDataException;
use App\Factory\HallFactory;
use App\Repository\HallRepository;
use App\Service\HallService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HallServiceTest extends WebTestCase
{
    protected HallFactory $factory;
    protected HallRepository $hallRepository;
    protected HallService $hallService;

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->factory = $container->get(HallFactory::class);
        $this->hallRepository = $container->get(HallRepository::class);
        $this->hallService = $container->get(HallService::class);
    }

    public function testFindReturnsHallIfItExists(): void
    {
        $hall = $this->factory->create();
        $id = $hall->getId();

        $foundHall = $this->hallService->find($id);

        $this->assertEquals($hall, $foundHall);
    }

    public function testFindThrowsEntityNotFoundExceptionIfHallDoesntExist(): void
    {
        $id = 0;

        $this->expectException(EntityNotFoundException::class);

        $this->hallService->find($id);
    }

    public function testFindAllReturnsAllHalls(): void
    {
        $halls = [$this->factory->create(), $this->factory->create()];

        $foundHalls = $this->hallService->findAll();

        $this->assertEquals($halls, $foundHalls);
    }

    public function testCreateCreatesHallIfDataIsValid(): void
    {
        $data = [
            'name' => 'A1',
            'capacity' => 1,
        ];

        $hall = $this->hallService->create($data);

        $this->assertEquals('A1', $hall->getName());
        $this->assertEquals(1, $hall->getCapacity());
    }

    public function testCreateThrowsInvalidDataExceptionIfDataIsInvalid(): void
    {
        $data = [
            'name' => 'A1',
            'capacity' => 0,
        ];

        $this->expectException(InvalidDataException::class);

        $this->hallService->create($data);

        $this->assertCount(0, $this->hallRepository->findAll());
    }

    public function testUpdateUpdatesHallIfDataIsValid(): void
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

    public function testUpdateThrowsInvalidDataExceptionIfDataIsInvalid(): void
    {
        $hall = $this->factory->create();
        $id = $hall->getId();
        $data = [
            'name' => 'A1',
            'capacity' => 0,
        ];

        $this->expectException(InvalidDataException::class);

        $this->hallService->update($id, $data);
    }

    public function testUpdateThrowsEntityNotFoundExceptionIfHallDoesntExist(): void
    {
        $id = 0;
        $data = [
            'name' => 'A1',
            'capacity' => 1,
        ];

        $this->expectException(EntityNotFoundException::class);

        $this->hallService->update($id, $data);
    }

    public function testDeleteDeletesHallIfItExists(): void
    {
        $hall = $this->factory->create();
        $id = $hall->getId();

        $this->hallService->delete($id);

        $this->assertCount(0, $this->hallRepository->findAll());
    }

    public function testDeleteThrowsEntityNotFoundExceptionIfHallDoesntExist(): void
    {
        $id = 0;

        $this->expectException(EntityNotFoundException::class);

        $this->hallService->delete($id);
    }
}
