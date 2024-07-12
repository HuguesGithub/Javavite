<?php
namespace src\Entity;

class Gear extends Entity
{
    protected int $gear;
    protected int $score;

    public function __construct(int $gear, int $score)
    {
        $this->gear = $gear;
        $this->score = $score;
    }

    public function getGear(): int
    {
        return $this->gear;
    }

    public function getScore(): int
    {
        return $this->score;
    }
}
