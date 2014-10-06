<?php

namespace Vellocet\SimpleBundle\Exception;

/**
 * Exceptions for validation
 * Class: ValidationException
 *
 * @see \Exception
 */
class ValidationException extends \Exception
{
    private $errors;

    public function __construct($errors)
    {
        $this->errors = $errors;
        parent::__construct();
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
