<?php

namespace App\Service;

use App\Entity\Movie;
use App\Exception\EntityNotFoundException;
use App\Exception\InvalidDataException;
use App\Repository\MovieRepository;
use App\Validator\MovieValidator;
use DateTime;
use Exception;
use Symfony\Component\Uid\Uuid;

class MovieService
{
    public function __construct(
        private readonly MovieRepository $repository,
        private readonly MovieValidator $validator
    ) {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function find(Uuid $id): Movie
    {
        $movie = $this->repository->find($id);

        if (!$movie) {
            throw new EntityNotFoundException('Movie', $id);
        }

        return $movie;
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @throws InvalidDataException
     * @throws Exception
     */
    public function create(array $data): Movie
    {
        $errors = $this->validator->validate($data);

        if (count($errors) !== 0) {
            throw new InvalidDataException($errors);
        }

        $movie = new Movie();

        $movie->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setLength(new DateTime($data['length']))
            ->setReleaseDate(new DateTime($data['release_date']));

        $this->repository->save($movie, true);

        return $movie;
    }

    /**
     * @throws InvalidDataException
     * @throws EntityNotFoundException
     * @throws Exception
     */
    public function update(Uuid $id, array $data): void
    {
        $errors = $this->validator->validate($data);

        if (count($errors) !== 0) {
            throw new InvalidDataException($errors);
        }

        $movie = $this->find($id);

        $movie->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setLength(new DateTime($data['length']))
            ->setReleaseDate(new DateTime($data['release_date']));

        $this->repository->save($movie, true);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(Uuid $id): void
    {
        $movie = $this->find($id);
        $this->repository->remove($movie, true);
    }
}