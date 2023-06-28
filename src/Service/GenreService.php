<?php

namespace App\Service;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class GenreService
{
    public function __construct(private readonly GenreRepository $genreRepository)
    {
    }

    public function findByName(string $name): Genre
    {
        $genre = $this->genreRepository->findOneBy(['name' => $name]);

        if ($genre === null) {
            throw new ResourceNotFoundException(
                'Genre with name '.$name.' does not exist'
            );
        }

        return $genre;
    }
}