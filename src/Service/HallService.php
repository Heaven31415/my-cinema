<?php

namespace App\Service;

use App\Entity\Hall;
use App\Repository\HallRepository;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class HallService
{
    public function __construct(private readonly HallRepository $hallRepository)
    {
    }

    public function find(int $id): Hall
    {
        $hall = $this->hallRepository->find($id);

        if (!$hall) {
            throw new ResourceNotFoundException("Hall with id $id doesn't exist");
        }

        return $hall;
    }

    /**
     * @return Hall[]
     */
    public function findAll(): array
    {
        return $this->hallRepository->findAll();
    }

    public function create(array $data): Hall
    {
        $hall = new Hall();

        $hall->setName($data['name'])
            ->setCapacity($data['capacity']);

        $this->hallRepository->save($hall, true);

        return $hall;
    }

    public function update(int $id, array $data): void
    {
        $hall = $this->find($id);

        $hall->setName($data['name'])
            ->setCapacity($data['capacity']);

        $this->hallRepository->save($hall, true);
    }

    public function delete(int $id): void
    {
        $hall = $this->find($id);

        $this->hallRepository->remove($hall, true);
    }
}