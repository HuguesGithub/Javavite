<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class DnfEvent extends Event
{
    private int $dnfPosition;

    public function __construct(array $params)
    {
        $this->type = $params[1] ?? '';
        $this->quantity = 1;
        $this->dnfPosition = $params[0];
    }

    public function __toString(): string
    {
        $str = parent::__toString();
        return $str . ConstantConstant::CST_TAB.'dnfPosition : '.$this->dnfPosition.ConstantConstant::CST_EOL;
    }

    public function getDnfPosition(): int
    {
        return $this->dnfPosition;
    }
}
