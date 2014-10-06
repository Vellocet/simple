<?php

namespace Vellocet\SimpleBundle\Helper;
use \Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Helper for validation
 * Class: ValidationHelper
 *
 */
class ValidationHelper
{
    public static function buildErrors(
        ConstraintViolationListInterface $violationList
    )
    {
        $errors = array();
        foreach ($violationList as $violation){
            $field = preg_replace('/\[|\]/', "", $violation->getPropertyPath());
            $error = $violation->getMessage();
            $errors[$field] = $error;
        }
        return $errors;
    }
}
