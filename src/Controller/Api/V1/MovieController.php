<?php

namespace App\Controller\Api\V1;

use App\Service\MovieService;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class MovieController extends AbstractFOSRestController
{
    public function __construct(private readonly MovieService $movieService)
    {
    }

    #[Get('/api/v1/movies', name: 'api_v1_index_movies')]
    #[ViewAnnotation(statusCode: Response::HTTP_OK, serializerGroups: ['basic'])]
    public function index(): View
    {
        return View::create($this->movieService->findAll());
    }

    #[Get('/api/v1/movies/{id}', name: 'api_v1_show_movie')]
    #[ViewAnnotation(statusCode: Response::HTTP_OK, serializerGroups: ['basic'])]
    public function show(Uuid $id): View
    {
        return View::create($this->movieService->find($id));
    }

    /**
     * @throws Exception
     */
    #[Post('/api/v1/movies', name: 'api_v1_create_movie')]
    #[ViewAnnotation(statusCode: Response::HTTP_CREATED, serializerGroups: ['basic'])]
    #[RequestParam(name: 'title', requirements: [
        new Assert\Type(['type' => 'string']),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'description', requirements: [
        new Assert\Type(['type' => 'string']),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'length', requirements: [
        new Assert\Time(),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'release_date', requirements: [
        new Assert\Date(),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'genre', requirements: [
        new Assert\Type(['type' => 'string']),
        new Assert\NotBlank(),
    ])]
    public function create(Request $request): View
    {
        return View::create($this->movieService->create($request->request->all()));
    }

    /**
     * @throws Exception
     */
    #[Put('/api/v1/movies/{id}', name: 'api_v1_update_movie')]
    #[RequestParam(name: 'title', requirements: [
        new Assert\Type(['type' => 'string']),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'description', requirements: [
        new Assert\Type(['type' => 'string']),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'length', requirements: [
        new Assert\Time(),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'release_date', requirements: [
        new Assert\Date(),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'genre', requirements: [
        new Assert\Type(['type' => 'string']),
        new Assert\NotBlank(),
    ])]
    public function update(Uuid $id, Request $request): View
    {
        $this->movieService->update($id, $request->request->all());

        return View::create();
    }

    #[Delete('/api/v1/movies/{id}', name: 'api_v1_delete_movie')]
    public function delete(Uuid $id): View
    {
        $this->movieService->delete($id);

        return View::create();
    }
}
