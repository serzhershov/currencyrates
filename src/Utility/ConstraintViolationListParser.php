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
            $messages[$paramName] = sprintf(
                "{%s} set to {%s} failed validation with: %s",
                $violation->getPropertyPath(),
                $violation->getInvalidValue(),
                implode(' ', $messages[$paramName])
            );
        }

        return count($messages) ? implode(" ", $messages) : '';
    }
}