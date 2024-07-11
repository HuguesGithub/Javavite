<?php
namespace src\Entity;

class Event extends Entity
{
    protected string $type;
    protected int $quantity;

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
