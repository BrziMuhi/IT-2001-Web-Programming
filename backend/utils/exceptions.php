<?php

class ValidationException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 400);
    }
}

class NotFoundException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 404);
    }
}

class UnauthorizedException extends Exception
{
    public function __construct($message = 'You must be logged in.')
    {
        parent::__construct($message, 401);
    }
}

class ForbiddenException extends Exception
{
    public function __construct($message = 'You do not have permission to do this.')
    {
        parent::__construct($message, 403);
    }
}
