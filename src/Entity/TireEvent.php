<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class TireEvent extends Event
{
    public function __construct(array $params)
    {
        $this->type = $params[0];
        $typePneus = trim($params[2]);

        // Selon $typePneus et la météo, on perd un certain nombre de pneus.
        if ($typePneus=='durs') {
            $this->quantity = $params[1];
        } elseif ($typePneus=='tendres') {
            $this->quantity = $params[1];
            // TODO : Si les pneus sont usagés, *3
            $this->quantity = 2*$params[1];
        } else {
            // pneus pluie
            // TODO : prendre en compte la météo et l'usage
            $this->quantity = $params[1];
        }
    }
}
