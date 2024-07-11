<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class TaqEvent extends Event
{

    public function __construct()
    {
        $this->type = '';
        $this->quantity = 1;
    }

}
