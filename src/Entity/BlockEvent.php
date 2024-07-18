<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class BlockEvent extends Event
{
    protected string $onPlayerName;

    public function __construct(string $playerName)
    {
        $this->type = '';
        $this->quantity = 1;

        // Joueur qui bloque le passage
        $this->onPlayerName = $playerName ?? '';
    }

    public function __toString(): string
    {
        $str = parent::__toString();
        return $str . ConstantConstant::CST_TAB.'par le joueur : '.$this->onPlayerName.ConstantConstant::CST_EOL;
    }
}
