<?php

namespace App\Exception;

use Exception;

class InvalidDataException extends Exception
{
    private array $errors;

    public function __construct(array $errors)
    {
        parent::__construct('Errors were found in provided data');
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}