<?php

namespace App\Service;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use DateTime;
use Exception;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Uid\Uuid;

class MovieService
{
    public function __construct(
        private readonly GenreService $genreService,
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
        return $this->movieRepository->findBy([], ['id' => 'ASC']);
    }

    /**
     * @throws Exception
     */
    public function create(array $data): Movie
    {
        $movie = new Movie();

        $genre = $this->genreService->findByName($data['genre']);

        $movie->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setDurationInMinutes($data['durationInMinutes'])
            ->setReleaseDate(new DateTime($data['releaseDate']));

        $genre->addMovie($movie);

        $this->movieRepository->save($movie, true);

        return $movie;
    }

    /**
     * @throws Exception
     */
    public function update(Uuid $id, array $data): void
    {
        $movie = $this->find($id);

        $newGenre = $this->genreService->findByName($data['genre']);

        $movie->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setDurationInMinutes($data['durationInMinutes'])
            ->setReleaseDate(new DateTime($data['releaseDate']));

        if ($movie->getGenre() !== $newGenre) {
            $movie->getGenre()->removeMovie($movie);
            $newGenre->addMovie($movie);
        }

        $this->movieRepository->save($movie, true);
    }

    public function delete(Uuid $id): void
    {
        $movie = $this->find($id);

        $this->movieRepository->remove($movie, true);
    }
}