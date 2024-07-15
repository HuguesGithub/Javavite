<?php
namespace src\Exception;

class KeyHasUseException extends \Exception
{
    public function __construct($key)
    {
        parent::__construct("Key $key already in use.", 0);
    }
}
