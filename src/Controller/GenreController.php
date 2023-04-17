<?php

namespace App\Controller;

use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    public function __construct(private readonly GenreRepository $repository)
    {
    }

    #[Route('/genres', name: 'index_genres', methods: 'GET')]
    public function index(): JsonResponse
    {
        return $this->json($this->repository->findAll(), Response::HTTP_OK, [], [
            'groups' => 'basic',
        ]);
    }
}
