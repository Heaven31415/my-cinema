<?php

namespace App\Service;

use App\Entity\Show;
use App\Repository\ShowRepository;
use DateTime;
use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Uid\Uuid;

class ShowService
{
    public function __construct(
        private readonly MovieService $movieService,
        private readonly HallService $hallService,
        private readonly ShowRepository $showRepository
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
     * @return Show[]
     */
    public function findAll(): array
    {
        return $this->showRepository->findBy([], ['id' => 'ASC']);
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