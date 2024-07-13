<?php
namespace src\Entity;

class Event extends Entity
{
    protected string $type;
    protected int $quantity;
    protected int $score = -1;

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

}
