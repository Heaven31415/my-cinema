<?php

namespace App\Controller\Api\V1;

use App\Service\HallService;
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
use Symfony\Component\Validator\Constraints as Assert;

class HallController extends AbstractFOSRestController
{
    public function __construct(private readonly HallService $hallService)
    {
    }

    #[Get('/api/v1/halls', name: 'api_v1_index_halls')]
    #[ViewAnnotation(statusCode: Response::HTTP_OK, serializerGroups: ['basic'])]
    public function index(): View
    {
        return View::create($this->hallService->findAll());
    }

    #[Get('/api/v1/halls/{id}', name: 'api_v1_show_hall')]
    #[ViewAnnotation(statusCode: Response::HTTP_OK, serializerGroups: ['basic'])]
    public function show(int $id): View
    {
        return View::create($this->hallService->find($id));
    }

    #[Post('/api/v1/halls', name: 'api_v1_create_hall')]
    #[ViewAnnotation(statusCode: Response::HTTP_CREATED, serializerGroups: ['basic'])]
    #[RequestParam(name: 'name', requirements: [
        new Assert\Type(['type' => 'string']),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'capacity', requirements: [
        new Assert\Type(['type' => 'integer']),
        new Assert\Positive(),
    ])]
    public function create(Request $request): View
    {
        return View::create($this->hallService->create($request->request->all()));
    }


    #[Put('/api/v1/halls/{id}', name: 'api_v1_update_hall')]
    #[RequestParam(name: 'name', requirements: [
        new Assert\Type(['type' => 'string']),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'capacity', requirements: [
        new Assert\Type(['type' => 'integer']),
        new Assert\Positive(),
    ])]
    public function update(int $id, Request $request): View
    {
        $this->hallService->update($id, $request->request->all());

        return View::create();
    }

    #[Delete('/api/v1/halls/{id}', name: 'api_v1_delete_hall')]
    public function delete(int $id): View
    {
        $this->hallService->delete($id);

        return View::create();
    }
}
