<?php

namespace App\Exception;

use Exception;
use Symfony\Component\Uid\Uuid;

class EntityNotFoundException extends Exception
{
    public function __construct(string $entityName, Uuid $id)
    {
        parent::__construct("Unable to find $entityName with id $id");
    }
}