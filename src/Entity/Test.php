<?php
namespace src\Entity;

class Test extends Entity
{
    // Indique si le jet est un échec
    protected bool $fail;
    protected int $score;
    protected bool $inflicted = false;

    public function getScore(): int
    {
        return $this->score;
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
