<?php

namespace App\Controller\Api\V1;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class GenreController extends AbstractFOSRestController
{
    public function __construct(private readonly GenreRepository $genreRepository)
    {
    }

    /**
     * Return a list of available movie genres
     */
    #[OA\Tag(name: 'genres')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Successful operation',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Genre::class, groups: ['basic']))
        )
    )]
    #[Get('/api/v1/genres', name: 'api_v1_index_genres')]
    #[ViewAnnotation(statusCode: Response::HTTP_OK, serializerGroups: ['basic'])]
    public function index(): View
    {
        return View::create($this->genreRepository->findAll());
    }
}
