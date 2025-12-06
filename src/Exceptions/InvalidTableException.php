<?php

namespace BehzadHosseinPoor\DatabaseManager\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidTableException extends HttpException
{
    /**
     * Create a new exception instance.
     *
     * @return static
     */
    public static function make(): static
    {
        return new static(400, 'Invalid TABLE');
    }
}