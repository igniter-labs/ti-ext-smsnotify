<?php

namespace IgniterLabs\SmsNotify\Exceptions;

class OTPException extends \Exception
{
    public $user;

    public $codeWasSent;

    public static function create($user, $codeWasSent)
    {
        $exception = new static;
        $exception->user = $user;
        $exception->codeWasSent = $codeWasSent;

        return $exception;
    }
}