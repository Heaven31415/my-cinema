<?php

namespace App\Controller\Api\V1;

use App\Entity\Hall;
use App\Service\HallService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\View as ViewAnnotation;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class HallController extends AbstractFOSRestController
{
    public function __construct(private readonly HallService $hallService)
    {
    }

    /**
     * Return a list of available halls
     */
    #[OA\Tag(name: 'halls')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Successful operation',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Hall::class, groups: ['basic']))
        )
    )]
    #[Get('/api/v1/halls', name: 'api_v1_index_halls')]
    #[ViewAnnotation(statusCode: Response::HTTP_OK, serializerGroups: ['basic'])]
    public function index(): View
    {
        return View::create($this->hallService->findAll());
    }

    /**
     * Return a single hall
     */
    #[OA\Tag(name: 'halls')]
    #[OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Successful operation',
        content: new Model(type: Hall::class, groups: ['basic'])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Hall not found'
    )]
    #[Get('/api/v1/halls/{id}', name: 'api_v1_show_hall')]
    #[ViewAnnotation(statusCode: Response::HTTP_OK, serializerGroups: ['basic'])]
    public function show(int $id): View
    {
        return View::create($this->hallService->find($id));
    }

    /**
     * Add a new hall
     */
    #[OA\Tag(name: 'halls')]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Successful operation',
        content: new Model(type: Hall::class, groups: ['basic'])
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Invalid request body'
    )]
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


    /**
     * Update an already existing hall
     */
    #[OA\Tag(name: 'halls')]
    #[OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Successful operation'
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Invalid request body'
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Hall not found'
    )]
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

    /**
     * Delete a single hall
     */
    #[OA\Tag(name: 'halls')]
    #[OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Successful operation'
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Hall not found'
    )]
    #[Delete('/api/v1/halls/{id}', name: 'api_v1_delete_hall')]
    public function delete(int $id): View
    {
        $this->hallService->delete($id);

        return View::create();
    }
}
