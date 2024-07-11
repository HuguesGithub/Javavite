<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class FuelEvent extends Event
{

    public function __construct(array $params)
    {
        $str = trim($params[0]);
        if (strpos($str, 'brusquement')!==false) {
            $this->type = ConstantConstant::CST_1GEAR;
        } elseif (strpos($str, 'violemment')) {
            $this->type = ConstantConstant::CST_2GEAR;
        } else {
            $this->type = ConstantConstant::CST_3GEAR;
        }

        $this->quantity = 1;
    }

}
