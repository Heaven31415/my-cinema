<?php

namespace App\DataFixtures;

use App\Service\MovieService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class MovieFixture extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['dev', 'prod'];
    }

    public function getDependencies(): array
    {
        return [GenreFixture::class];
    }

    public function __construct(private readonly MovieService $movieService)
    {
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $moviesData = [
            [
                'title' => 'Avengers: Endgame',
                'description' => 'The Avengers\' final battle against Thanos...',
                'durationInMinutes' => 181,
                'releaseDate' => '2019-04-26',
                'genre' => 'Action',
            ],
            [
                'title' => 'Black Panther',
                'description' => 'T\'Challa becomes the Black Panther and fights for his nation...',
                'durationInMinutes' => 134,
                'releaseDate' => '2018-02-16',
                'genre' => 'Action',
            ],
            [
                'title' => 'Casablanca',
                'description' => 'A love triangle set during World War II...',
                'durationInMinutes' => 102,
                'releaseDate' => '1942-11-26',
                'genre' => 'Romance',
            ],
            [
                'title' => 'Deadpool',
                'description' => 'A wisecracking mercenary seeks revenge...',
                'durationInMinutes' => 108,
                'releaseDate' => '2016-02-12',
                'genre' => 'Action',
            ],
            [
                'title' => 'Eternal Sunshine of the Spotless Mind',
                'description' => 'A man undergoes a procedure to erase memories of a failed relationship...',
                'durationInMinutes' => 108,
                'releaseDate' => '2004-03-19',
                'genre' => 'Drama',
            ],
            [
                'title' => 'Forrest Gump',
                'description' => 'The life journey of a man with low IQ...',
                'durationInMinutes' => 142,
                'releaseDate' => '1994-07-06',
                'genre' => 'Drama',
            ],
            [
                'title' => 'Gladiator',
                'description' => 'A former Roman general seeks revenge...',
                'durationInMinutes' => 155,
                'releaseDate' => '2000-05-01',
                'genre' => 'Action',
            ],
            [
                'title' => 'Harry Potter and the Philosopher\'s Stone',
                'description' => 'A young wizard discovers his magical heritage...',
                'durationInMinutes' => 152,
                'releaseDate' => '2001-11-16',
                'genre' => 'Fantasy',
            ],
            [
                'title' => 'Inception',
                'description' => 'A thief enters dreams to steal information...',
                'durationInMinutes' => 148,
                'releaseDate' => '2010-07-16',
                'genre' => 'Science Fiction',
            ],
            [
                'title' => 'Jurassic Park',
                'description' => 'An amusement park with cloned dinosaurs...',
                'durationInMinutes' => 127,
                'releaseDate' => '1993-06-11',
                'genre' => 'Adventure',
            ],
        ];

        foreach ($moviesData as $moviesDatum) {
            $this->movieService->create($moviesDatum);
        }
    }
}