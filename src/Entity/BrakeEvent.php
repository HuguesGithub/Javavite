<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class BrakeEvent extends Event
{
    public function __construct(array $params)
    {
        $this->type = $params[0];
        $this->quantity = $params[1];
    }
}
