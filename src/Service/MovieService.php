<?php

namespace App\Service;

use App\Entity\Movie;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use DateTime;
use Exception;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Uid\Uuid;

class MovieService
{
    public function __construct(
        private readonly GenreRepository $genreRepository,
        private readonly MovieRepository $movieRepository
    ) {
    }

    public function find(Uuid $id): Movie
    {
        $movie = $this->movieRepository->find($id);

        if (!$movie) {
            throw new ResourceNotFoundException('Movie with id '.$id.' does not exist');
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
     * @throws Exception
     */
    public function create(array $data): Movie
    {
        $movie = new Movie();

        $this->updateAndSave($movie, $data);

        return $movie;
    }

    /**
     * @throws Exception
     */
    public function update(Uuid $id, array $data): void
    {
        $movie = $this->find($id);

        $this->updateAndSave($movie, $data);
    }

    public function delete(Uuid $id): void
    {
        $movie = $this->find($id);

        $this->movieRepository->remove($movie, true);
    }

    /**
     * @throws Exception
     */
    private function updateAndSave(Movie $movie, array $data): void
    {
        $genre = $this->genreRepository->findOneBy(['name' => $data['genre']]);

        if ($genre === null) {
            throw new ResourceNotFoundException(
                'Genre with name '.$data['genre'].' does not exist'
            );
        }

        $movie->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setLength(new DateTime($data['length']))
            ->setReleaseDate(new DateTime($data['releaseDate']))
            ->setGenre($genre);

        $this->movieRepository->save($movie, true);
    }
}