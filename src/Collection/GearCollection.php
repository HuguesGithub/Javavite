<?php
namespace src\Collection;

class GearCollection extends Collection
{
    public function countItems(int $gear, int $score): int
    {
        $nb = 0;
        $this->rewind();
        while ($this->valid()) {
            $objGear = $this->current();
            if ($objGear->getGear()==$gear && $objGear->getScore()==$score) {
                $nb++;
            }
            $this->next();
        }
        return $nb;
    }
}
