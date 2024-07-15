<?php
namespace src\Entity;

class GearEvent extends Event
{

    public function __construct(array $params)
    {
        $this->type = $params[0];
        $this->score = $params[1];
        $this->quantity = 1;
    }

    public function getScore(): int
    {
        return $this->score;
    }
}
