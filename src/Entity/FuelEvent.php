<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class FuelEvent extends Event
{

    public function __construct(array $params)
    {
        $this->type = ConstantConstant::CST_FUEL;

        $subType = trim($params[0]);
        if (mb_substr($subType, 0, 11)=='brusquement') {
            $this->subType = ConstantConstant::CST_1GEAR;
        } elseif (mb_substr($subType, 5, 10)=='violemment') {
            $this->subType = ConstantConstant::CST_2GEAR;
        } else {
            echo $subType.'<br>';
        }

        $this->quantity = 1;
    }

}
