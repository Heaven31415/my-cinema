<?php

namespace App\Service;

use App\Entity\Hall;
use App\Exception\EntityNotFoundException;
use App\Exception\InvalidDataException;
use App\Repository\HallRepository;
use App\Validator\HallValidator;

class HallService
{
    public function __construct(
        private readonly HallRepository $hallRepository,
        private readonly HallValidator $validator
    ) {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function find(int $id): Hall
    {
        $hall = $this->hallRepository->find($id);

        if (!$hall) {
            throw new EntityNotFoundException('Hall');
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

    /**
     * @throws InvalidDataException
     */
    public function create(array $data): Hall
    {
        $errors = $this->validator->validate($data);

        if (count($errors) !== 0) {
            throw new InvalidDataException($errors);
        }

        $hall = new Hall();
        $hall->setName($data['name'])
            ->setCapacity($data['capacity']);

        $this->hallRepository->save($hall, true);

        return $hall;
    }

    /**
     * @throws InvalidDataException
     * @throws EntityNotFoundException
     */
    public function update(int $id, array $data): void
    {
        $errors = $this->validator->validate($data);

        if (count($errors) !== 0) {
            throw new InvalidDataException($errors);
        }

        $hall = $this->find($id);
        $hall->setName($data['name'])
            ->setCapacity($data['capacity']);

        $this->hallRepository->save($hall, true);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(int $id): void
    {
        $hall = $this->find($id);
        $this->hallRepository->remove($hall, true);
    }
}