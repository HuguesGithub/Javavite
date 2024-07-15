<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class TestEvent extends Event
{
    // Indique si le jet est un échec
    protected bool $fail;
    protected bool $inflicted = false;

    public function __toString(): string
    {
        $str  = parent::__toString();
        $str .= ConstantConstant::CST_TAB.'fail : '.($this->fail?'true':'false').ConstantConstant::CST_EOL;
        return $str . ConstantConstant::CST_TAB.'inflicted : '.($this->fail?'true':'false').ConstantConstant::CST_EOL;
    }

    public function isFail(): bool
    {
        return $this->fail;
    }

    public function setInflicted(bool $inflicted): void
    {
        $this->inflicted = $inflicted;
    }

    public function isInflicted(): bool
    {
        return $this->inflicted;
    }
}
