<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class TireEvent extends Event
{
    public function __construct(array $params)
    {
        $this->type = ConstantConstant::CST_TIRE;
        $this->subType = $params[0];
        $this->quantity = $params[1];
    }
}
