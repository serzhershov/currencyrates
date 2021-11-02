<?php

namespace App\Utility;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationListParser
{
    /**
     * @param ConstraintViolationListInterface $violations
     * @return string
     */
    public static function getString(ConstraintViolationListInterface $violations): string
    {
        $messages = [];

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $paramName = $violation->getPropertyPath();
            $messages[$paramName][] = $violation->getMessage();
            $messages[$paramName] = $violation->getPropertyPath() . ' - ' . implode(' ', $messages[$paramName]);
        }

        return count($messages) ? implode(" ", $messages) : '';
    }
}