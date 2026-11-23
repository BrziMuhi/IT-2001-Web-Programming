<?php

class HttpException extends Exception
{
    protected $status;

    public function __construct($message, $status)
    {
        parent::__construct($message, $status);
        $this->status = $status;
    }

    public function get_status()
    {
        return $this->status;
    }
}

class ValidationException extends HttpException
{
    public function __construct($message)
    {
        parent::__construct($message, 400);
    }
}

class NotFoundException extends HttpException
{
    public function __construct($message)
    {
        parent::__construct($message, 404);
    }
}

class UnauthorizedException extends HttpException
{
    public function __construct($message = 'You must be logged in.')
    {
        parent::__construct($message, 401);
    }
}

class ForbiddenException extends HttpException
{
    public function __construct($message = 'You do not have permission to do this.')
    {
        parent::__construct($message, 403);
    }
}
