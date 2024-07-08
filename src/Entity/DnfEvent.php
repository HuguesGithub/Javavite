<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class DnfEvent extends Event
{
    private int $dnfPosition;

    public function __construct(array $params)
    {
        $this->type = ConstantConstant::CST_DNF;
        $this->subType = '';
        $this->quantity = 1;
        $this->dnfPosition = $params[0];
    }

    public function getDnfPosition(): int
    {
        return $this->dnfPosition;
    }
}
