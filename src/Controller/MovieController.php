<?php

namespace App\Controller;

use App\Exception\EntityNotFoundException;
use App\Exception\InvalidDataException;
use App\Service\MovieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class MovieController extends AbstractController
{
    public function __construct(private readonly MovieService $movieService)
    {
    }

    #[Route('/movies', name: 'index_movies', methods: 'GET')]
    public function index(): JsonResponse
    {
        return $this->json($this->movieService->findAll(), Response::HTTP_OK, [], [
            'groups' => 'basic',
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/movies/{id}', name: 'show_movie', methods: 'GET')]
    public function show(Uuid $id): JsonResponse
    {
        return $this->json($this->movieService->find($id), Response::HTTP_OK, [], [
            'groups' => 'basic',
        ]);
    }

    /**
     * @throws InvalidDataException
     */
    #[Route('/movies', name: 'create_movie', methods: 'POST')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $movie = $this->movieService->create($data);

        return $this->json($movie, Response::HTTP_CREATED, [], [
            'groups' => 'basic',
        ]);
    }

    /**
     * @throws InvalidDataException
     * @throws EntityNotFoundException
     */
    #[Route('/movies/{id}', name: 'update_movie', methods: 'PUT')]
    public function update(Uuid $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->movieService->update($id, $data);

        $content = ['message' => 'Movie was successfully updated.'];

        return $this->json($content, Response::HTTP_OK, [], [
            'groups' => 'basic',
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/movies/{id}', name: 'delete_movie', methods: 'DELETE')]
    public function delete(Uuid $id): JsonResponse
    {
        $this->movieService->delete($id);

        $content = ['message' => 'Movie was successfully deleted.'];

        return $this->json($content, Response::HTTP_OK, [], [
            'groups' => 'basic',
        ]);
    }
}
