<?php

namespace App\Controller\Api\V1;

use App\Entity\Show;
use App\Service\ShowService;
use Exception;
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

class ShowController extends AbstractFOSRestController
{
    public function __construct(private readonly ShowService $showService)
    {
    }

    /**
     * Return a list of available shows
     */
    #[OA\Tag(name: 'shows')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Successful operation',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Show::class, groups: ['basic']))
        )
    )]
    #[Get('/api/v1/shows', name: 'api_v1_index_shows')]
    #[ViewAnnotation(statusCode: Response::HTTP_OK, serializerGroups: ['basic'])]
    public function index(): View
    {
        return View::create($this->showService->findAll());
    }

    /**
     * Return a single show
     */
    #[OA\Tag(name: 'shows')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Successful operation',
        content: new Model(type: Show::class, groups: ['basic'])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Show not found'
    )]
    #[Get('/api/v1/shows/{id}', name: 'api_v1_show_show')]
    #[ViewAnnotation(statusCode: Response::HTTP_OK, serializerGroups: ['basic'])]
    public function show(int $id): View
    {
        return View::create($this->showService->find($id));
    }

    /**
     * Add a new show
     *
     * @throws Exception
     */
    #[OA\Tag(name: 'shows')]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Successful operation',
        content: new Model(type: Show::class, groups: ['basic'])
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Invalid request body'
    )]
    #[Post('/api/v1/shows', name: 'api_v1_create_show')]
    #[ViewAnnotation(statusCode: Response::HTTP_CREATED, serializerGroups: ['basic'])]
    #[RequestParam(name: 'movie', requirements: [
        new Assert\Type(['type' => 'string']),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'hall', requirements: [
        new Assert\Type(['type' => 'integer']),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'startTime', requirements: [
        new Assert\DateTime(),
        new Assert\NotBlank(),
    ])]
    public function create(Request $request): View
    {
        return View::create($this->showService->create($request->request->all()));
    }

    /**
     * Update an already existing show
     *
     * @throws Exception
     */
    #[OA\Tag(name: 'shows')]
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
        description: 'Show not found'
    )]
    #[Put('/api/v1/shows/{id}', name: 'api_v1_update_show')]
    #[RequestParam(name: 'movie', requirements: [
        new Assert\Type(['type' => 'string']),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'hall', requirements: [
        new Assert\Type(['type' => 'integer']),
        new Assert\NotBlank(),
    ])]
    #[RequestParam(name: 'startTime', requirements: [
        new Assert\DateTime(),
        new Assert\NotBlank(),
    ])]
    public function update(int $id, Request $request): View
    {
        $this->showService->update($id, $request->request->all());

        return View::create();
    }

    /**
     * Delete a single show
     */
    #[OA\Tag(name: 'shows')]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Successful operation'
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Show not found'
    )]
    #[Delete('/api/v1/shows/{id}', name: 'api_v1_delete_show')]
    public function delete(int $id): View
    {
        $this->showService->delete($id);

        return View::create();
    }
}
