<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class TrailEvent extends Event
{
    protected string $onPlayerName;

    public function __construct(array $params=[])
    {
        $this->type = '';
        $this->quantity = 1;

        // Joueur sur qui est prise l'aspiration
        $this->onPlayerName = $params[0] ?? '';
    }

    public function __toString(): string
    {
        $str = parent::__construct();
        return $str . ConstantConstant::CST_TAB.'sur le joueur : '.$this->onPlayerName.ConstantConstant::CST_EOL;
    }
}
