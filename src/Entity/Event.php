<?php
namespace src\Entity;

class Event extends Entity
{
    protected string $type;
    protected string $subType;
    protected int $quantity;

    public function getType(): string
    {
        return $this->type;
    }

    public function getSubType(): string
    {
        return $this->subType;
    }

    public function setSubType(string $subType): void
    {
        $this->subType = $subType;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

}
