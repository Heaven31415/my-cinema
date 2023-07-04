<?php

namespace App\ApiDoc\RouteDescriber;

use Nelmio\ApiDocBundle\RouteDescriber\RouteDescriberInterface;
use Nelmio\ApiDocBundle\RouteDescriber\RouteDescriberTrait;
use OpenApi\Annotations\OpenApi;
use ReflectionMethod;
use Symfony\Component\Routing\Route;

/**
 * Author: jlekowski (Jerzy Lekowski)
 * Source: https://github.com/nelmio/NelmioApiDocBundle/issues/1775
 *
 * Workaround for the following warning:
 * Multiple @OA\MediaType() with the same mediaType="application/json"
 */
class DuplicatedRequestBodyNormalizer implements RouteDescriberInterface
{
    use RouteDescriberTrait;

    /**
     * @param OpenApi          $api
     * @param Route            $route
     * @param ReflectionMethod $reflectionMethod
     */
    public function describe(OpenApi $api, Route $route, ReflectionMethod $reflectionMethod): void
    {
        foreach ($this->getOperations($api, $route) as $operation) {
            if (count($operation->requestBody->content ?? []) > 1) {
                // Remove operation created by FosRestDescriber if PhpDocDescriber one is present
                unset($operation->requestBody->content["application/json"]);
            }
        }
    }
}