<?php

namespace Cintas\Exceptions;

use Exception;
use Throwable;

class NoteworthyException extends Exception
{
    public $severity;

    public function __construct($message = "", $code = 0, Throwable $previous = null, $severity = 'error')
    {
        parent::__construct($message, $code, $previous);
        $this->severity = $severity;
    }

}
