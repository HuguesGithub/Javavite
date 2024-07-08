<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class TaqEvent extends Event
{

    public function __construct()
    {
        $this->type = ConstantConstant::CST_TAQ;
        $this->subType = '';
        $this->quantity = 1;
    }

}
