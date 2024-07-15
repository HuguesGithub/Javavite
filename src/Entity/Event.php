<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class Event extends Entity
{
    protected string $type;
    protected int $quantity;
    protected int $score = -1;

    public function __toString(): string
    {
        $str  = $this::class.ConstantConstant::CST_EOL;
        $str .= ConstantConstant::CST_TAB.'type : '.$this->type.ConstantConstant::CST_EOL;
        $str .= ConstantConstant::CST_TAB.'quantity : '.$this->quantity.ConstantConstant::CST_EOL;
        return $str . ConstantConstant::CST_TAB.'score : '.$this->score.ConstantConstant::CST_EOL;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getScore(): int
    {
        return $this->score;
    }

}
