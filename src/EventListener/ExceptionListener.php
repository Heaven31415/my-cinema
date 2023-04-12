<?php

namespace App\EventListener;

use App\Exception\EntityNotFoundException;
use App\Exception\InvalidDataException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsEventListener]
class ExceptionListener
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof InvalidDataException) {
            $event->setResponse(
                new JsonResponse($exception->getErrors(), Response::HTTP_BAD_REQUEST)
            );
        } elseif ($exception instanceof EntityNotFoundException) {
            $event->setResponse(
                new JsonResponse(['error' => $exception->getMessage()],
                    Response::HTTP_NOT_FOUND)
            );
        } elseif ($this->kernel->getEnvironment() === 'prod') {
            $event->setResponse(
                new JsonResponse(
                    ['error' => 'Something bad happened on our side, sorry for that!'],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                )
            );
        }
    }
}