<?php

namespace App\Service;

use App\Entity\Show;
use App\Repository\ShowRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Uid\Uuid;

class ShowService
{
    public function __construct(
        private readonly MovieService $movieService,
        private readonly HallService $hallService,
        private readonly ShowRepository $showRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function find(int $id): Show
    {
        $show = $this->showRepository->find($id);

        if (!$show) {
            throw new ResourceNotFoundException('Show with id '.$id.' does not exist');
        }

        return $show;
    }

    /**
     * @param string|null $title Case-sensitive fragment of the movie title
     * @param string|null $genre Case-sensitive fragment of the genre name
     * @param string|null $from  Date in YYYY-mm-dd format (inclusive)
     * @param string|null $to    Date in YYYY-mm-dd format (exclusive)
     *
     * @return Show[]
     */
    public function findAll(
        ?string $title = null,
        ?string $genre = null,
        ?string $from = null,
        ?string $to = null
    ): array {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('s')
            ->from('App\Entity\Show', 's')
            ->join('s.movie', 'm')
            ->join('m.genre', 'g');

        $and = $qb->expr()->andX();

        if ($title !== null) {
            $and->add($qb->expr()->like('m.title', ':title'));
            $qb->setParameter('title', '%'.$title.'%');
        }

        if ($genre !== null) {
            $and->add($qb->expr()->like('g.name', ':genre'));
            $qb->setParameter('genre', '%'.$genre.'%');
        }

        if ($from !== null) {
            $and->add($qb->expr()->gt('s.startTime', ':from'));
            $qb->setParameter('from', $from);
        }

        if ($to !== null) {
            $and->add($qb->expr()->lt('s.startTime', ':to'));
            $qb->setParameter('to', $to);
        }

        if ($and->count() !== 0) {
            $qb->where($and);
        }

        $qb->orderBy('s.startTime', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws Exception
     */
    public function create(array $data): Show
    {
        $movie = $this->movieService->find(Uuid::fromString($data['movie']));
        $hall = $this->hallService->find($data['hall']);

        $startTime = new DateTime($data['startTime']);

        if (!$hall->canPlayMovie($startTime, $movie)) {
            throw new BadRequestHttpException(
                'Hall "'.$hall->getName().'" is not available during that time'
            );
        }

        $show = new Show();

        $show->setStartTime($startTime);

        $movie->addShow($show);
        $hall->addShow($show);

        $this->showRepository->save($show, true);

        return $show;
    }

    /**
     * @throws Exception
     */
    public function update(int $id, array $data): void
    {
        $show = $this->find($id);

        $movie = $this->movieService->find(Uuid::fromString($data['movie']));
        $hall = $this->hallService->find($data['hall']);
        $startTime = new DateTime($data['startTime']);

        if (!$hall->canPlayMovie($startTime, $movie, $show)) {
            throw new BadRequestHttpException(
                'Hall "'.$hall->getName().'" is not available during that time'
            );
        }

        $show->setStartTime($startTime);

        if ($show->getMovie() !== $movie) {
            $show->getMovie()->removeShow($show);
            $movie->addShow($show);
        }

        if ($show->getHall() !== $hall) {
            $show->getHall()->removeShow($show);
            $hall->addShow($show);
        }

        $this->showRepository->save($show, true);
    }

    public function delete(int $id): void
    {
        $show = $this->find($id);

        $this->showRepository->remove($show, true);
    }
}