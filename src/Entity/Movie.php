<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[OA\Property(type: 'uuid', example: '8a47fd24-34d3-4ed0-b69c-4d151bf277c6')]
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups('basic')]
    private ?Uuid $id = null;

    #[OA\Property(minLength: 1, example: 'Avatar')]
    #[ORM\Column(length: 255)]
    #[Groups('basic')]
    private ?string $title = null;

    #[OA\Property(minLength: 1, example: 'Avatar is a 2009 science fiction film...')]
    #[ORM\Column(type: Types::TEXT)]
    #[Groups('basic')]
    private ?string $description = null;

    #[OA\Property(type: 'time', example: '02:42:00')]
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s'])]
    #[Groups('basic')]
    private ?DateTimeInterface $duration = null;

    #[OA\Property(type: 'date', example: '2009-12-25')]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Groups('basic')]
    private ?DateTimeInterface $releaseDate = null;

    #[ORM\ManyToOne(inversedBy: 'movies')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('basic')]
    private ?Genre $genre = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): ?DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(DateTimeInterface $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getReleaseDate(): ?DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }
}
