<?php

namespace App\Entity;

use App\Repository\HallRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HallRepository::class)]
class Hall
{
    #[OA\Property(example: 1)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('basic')]
    private ?int $id = null;

    #[OA\Property(minLength: 1, example: 'A1')]
    #[ORM\Column(length: 255)]
    #[Groups('basic')]
    private ?string $name = null;

    #[OA\Property(minimum: 1, example: 25)]
    #[ORM\Column]
    #[Groups('basic')]
    private ?int $capacity = null;

    #[ORM\OneToMany(mappedBy: 'hall', targetEntity: Show::class)]
    private Collection $shows;

    public function __construct()
    {
        $this->shows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * @return Collection<int, Show>
     */
    public function getShows(): Collection
    {
        return $this->shows;
    }

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return Collection<int, Show>
     */
    public function getShowsForTimeInterval(
        DateTimeInterface $from,
        DateTimeInterface $to
    ): Collection {
        return $this->shows->filter(p: function (Show $show) use ($from, $to) {
            $start = $show->getStartTime();
            $end = $show->getEndTime();

            return ($from <= $start && $start <= $to) || ($from <= $end && $end <= $to);
        });
    }

    public function canPlayMovie(DateTimeInterface $startTime, Movie $movie): bool
    {
        $durationInMinutes = $movie->getDurationInMinutes();

        $from = DateTimeImmutable::createFromInterface($startTime);
        $to = $from->modify('+ '.$durationInMinutes.' minutes');

        return count($this->getShowsForTimeInterval($from, $to)) === 0;
    }

    public function addShow(Show $show): self
    {
        if (!$this->shows->contains($show)) {
            $this->shows->add($show);
            $show->setHall($this);
        }

        return $this;
    }

    public function removeShow(Show $show): self
    {
        if ($this->shows->removeElement($show)) {
            // set the owning side to null (unless already changed)
            if ($show->getHall() === $this) {
                $show->setHall(null);
            }
        }

        return $this;
    }
}
