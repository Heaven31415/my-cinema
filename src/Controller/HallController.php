<?php

namespace App\Controller;

use App\Exception\EntityNotFoundException;
use App\Exception\InvalidDataException;
use App\Service\HallService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HallController extends AbstractController
{
    public function __construct(private readonly HallService $hallService)
    {
    }

    #[Route('/halls', name: 'index_halls', methods: 'GET')]
    public function index(): JsonResponse
    {
        return $this->json($this->hallService->findAll(), Response::HTTP_OK, [], [
            'groups' => 'basic',
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/halls/{id}', name: 'show_hall', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        return $this->json($this->hallService->find($id), Response::HTTP_OK, [], [
            'groups' => 'basic',
        ]);
    }

    /**
     * @throws InvalidDataException
     */
    #[Route('/halls', name: 'create_hall', methods: 'POST')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $hall = $this->hallService->create($data);

        return $this->json($hall, Response::HTTP_CREATED, [], [
            'groups' => 'basic',
        ]);
    }

    /**
     * @throws InvalidDataException
     * @throws EntityNotFoundException
     */
    #[Route('/halls/{id}', name: 'update_hall', methods: 'PUT')]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->hallService->update($id, $data);

        $content = ['message' => 'Hall was successfully updated.'];

        return $this->json($content, Response::HTTP_OK, [], [
            'groups' => 'basic',
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/halls/{id}', name: 'delete_hall', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $this->hallService->delete($id);

        $content = ['message' => 'Hall was successfully deleted.'];

        return $this->json($content, Response::HTTP_OK, [], [
            'groups' => 'basic',
        ]);
    }
}
