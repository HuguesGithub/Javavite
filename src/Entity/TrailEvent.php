<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class TrailEvent extends Event
{
    public function __construct(array $params)
    {
        $this->type = ConstantConstant::CST_TRAIL;
        $this->subType = '';
        $this->quantity = 1;

        // Joueur sur qui est prise l'aspiration
        $objPlayer = $params[0];
    }
}
