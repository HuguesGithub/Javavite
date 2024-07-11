<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class TrailEvent extends Event
{
    public function __construct()
    {
        $this->type = '';
        $this->quantity = 1;

        // Joueur sur qui est prise l'aspiration
        // TODO prendre en compte
        // $objPlayer = $params[0];
    }
}
