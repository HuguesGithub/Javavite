<?php
namespace src\Exception;

class KeyInvalidException extends \Exception
{
    public function __construct($key)
    {
        parent::__construct("Invalid key $key.", 0);
    }
}
