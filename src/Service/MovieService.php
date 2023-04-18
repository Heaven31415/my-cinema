<?php

namespace App\Service;

use App\Entity\Movie;
use App\Exception\EntityNotFoundException;
use App\Exception\InvalidDataException;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use App\Validator\MovieValidator;
use DateTime;
use Exception;
use Symfony\Component\Uid\Uuid;

class MovieService
{
    public function __construct(
        private readonly GenreRepository $genreRepository,
        private readonly MovieRepository $movieRepository,
        private readonly MovieValidator $validator
    ) {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function find(Uuid $id): Movie
    {
        $movie = $this->movieRepository->find($id);

        if (!$movie) {
            throw new EntityNotFoundException('Movie');
        }

        return $movie;
    }

    /**
     * @return Movie[]
     */
    public function findAll(): array
    {
        return $this->movieRepository->findAll();
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
        $this->updateAndSave($movie, $data);

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
        $this->updateAndSave($movie, $data);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(Uuid $id): void
    {
        $movie = $this->find($id);
        $this->movieRepository->remove($movie, true);
    }

    /**
     * @throws EntityNotFoundException
     * @throws Exception
     */
    private function updateAndSave(Movie $movie, array $data): void
    {
        $genre = $this->genreRepository->findOneBy(['name' => $data['genre']]);

        if ($genre === null) {
            throw new EntityNotFoundException('Genre');
        }

        $movie->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setLength(new DateTime($data['length']))
            ->setReleaseDate(new DateTime($data['release_date']))
            ->setGenre($genre);

        $this->movieRepository->save($movie, true);
    }
}