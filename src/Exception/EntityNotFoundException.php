<?php

namespace App\Exception;

use Exception;

class EntityNotFoundException extends Exception
{
    public function __construct(string $entityName)
    {
        parent::__construct("Unable to find $entityName");
    }
}