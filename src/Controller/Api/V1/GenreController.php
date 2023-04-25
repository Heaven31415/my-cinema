<?php

namespace App\Controller\Api\V1;

use App\Repository\GenreRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

class GenreController extends AbstractFOSRestController
{
    public function __construct(private readonly GenreRepository $genreRepository)
    {
    }

    #[Get('/api/v1/genres', name: 'api_v1_index_genres')]
    #[ViewAnnotation(statusCode: Response::HTTP_OK, serializerGroups: ['basic'])]
    public function index(): View
    {
        return View::create($this->genreRepository->findAll());
    }
}
