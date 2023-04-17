<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MovieValidator
{
    private Assert\Collection $constraint;

    public function __construct(private readonly ValidatorInterface $validator)
    {
        $this->constraint = new Assert\Collection([
            'title' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string']),
            ],
            'description' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string']),
            ],
            'length' => [
                new Assert\NotBlank(),
                new Assert\Time(),
            ],
            'release_date' => [
                new Assert\NotBlank(),
                new Assert\Date(),
            ],
            'genre' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string'])
            ]
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