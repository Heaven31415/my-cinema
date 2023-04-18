<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HallValidator
{
    private Assert\Collection $constraint;

    public function __construct(private readonly ValidatorInterface $validator)
    {
        $this->constraint = new Assert\Collection([
            'name' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string']),
            ],
            'capacity' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'integer']),
                new Assert\Positive(),
            ],
        ]);
    }

    public function validate(array $data): array
    {
        $violations = $this->validator->validate($data, $this->constraint);
        $errors = [];

        if (count($violations) !== 0) {
            foreach ($violations as $violation) {
                $errors[substr($violation->getPropertyPath(), 1, -1)] = $violation->getMessage();
            }
        }

        return $errors;
    }
}