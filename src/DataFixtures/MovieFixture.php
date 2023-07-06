<?php

namespace App\DataFixtures;

use App\Factory\GenreFactory;
use App\Factory\MovieFactory;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

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

    public function load(ObjectManager $manager): void
    {
        MovieFactory::createSequence([
            [
                'title' => 'Avengers: Endgame',
                'description' => 'The Avengers\' final battle against Thanos...',
                'durationInMinutes' => 181,
                'releaseDate' => new DateTime('2019-04-26'),
                'genre' => GenreFactory::find(['name' => 'Action']),
            ],
            [
                'title' => 'Black Panther',
                'description' => 'T\'Challa becomes the Black Panther and fights for his nation...',
                'durationInMinutes' => 134,
                'releaseDate' => new DateTime('2018-02-16'),
                'genre' => GenreFactory::find(['name' => 'Action']),
            ],
            [
                'title' => 'Casablanca',
                'description' => 'A love triangle set during World War II...',
                'durationInMinutes' => 102,
                'releaseDate' => new DateTime('1942-11-26'),
                'genre' => GenreFactory::find(['name' => 'Romance']),
            ],
            [
                'title' => 'Deadpool',
                'description' => 'A wisecracking mercenary seeks revenge...',
                'durationInMinutes' => 108,
                'releaseDate' => new DateTime('2016-02-12'),
                'genre' => GenreFactory::find(['name' => 'Action']),
            ],
            [
                'title' => 'Eternal Sunshine of the Spotless Mind',
                'description' => 'A man undergoes a procedure to erase memories of a failed relationship...',
                'durationInMinutes' => 108,
                'releaseDate' => new DateTime('2004-03-19'),
                'genre' => GenreFactory::find(['name' => 'Drama']),
            ],
            [
                'title' => 'Forrest Gump',
                'description' => 'The life journey of a man with low IQ...',
                'durationInMinutes' => 142,
                'releaseDate' => new DateTime('1994-07-06'),
                'genre' => GenreFactory::find(['name' => 'Drama']),
            ],
            [
                'title' => 'Gladiator',
                'description' => 'A former Roman general seeks revenge...',
                'durationInMinutes' => 155,
                'releaseDate' => new DateTime('2000-05-01'),
                'genre' => GenreFactory::find(['name' => 'Action']),
            ],
            [
                'title' => 'Harry Potter and the Philosopher\'s Stone',
                'description' => 'A young wizard discovers his magical heritage...',
                'durationInMinutes' => 152,
                'releaseDate' => new DateTime('2001-11-16'),
                'genre' => GenreFactory::find(['name' => 'Fantasy']),
            ],
            [
                'title' => 'Inception',
                'description' => 'A thief enters dreams to steal information...',
                'durationInMinutes' => 148,
                'releaseDate' => new DateTime('2010-07-16'),
                'genre' => GenreFactory::find(['name' => 'Science Fiction']),
            ],
            [
                'title' => 'Jurassic Park',
                'description' => 'An amusement park with cloned dinosaurs...',
                'durationInMinutes' => 127,
                'releaseDate' => new DateTime('1993-06-11'),
                'genre' => GenreFactory::find(['name' => 'Adventure']),
            ],
        ]);
    }
}