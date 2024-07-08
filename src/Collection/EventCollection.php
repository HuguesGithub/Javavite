<?php
namespace src\Collection;

class EventCollection extends Collection
{

    public function filterBy(string $typeEvent): EventCollection
    {
        $filtered = new EventCollection();
        $this->rewind();
        while ($this->valid()) {
            $objEvent = $this->current();
            if ($objEvent::class==$typeEvent) {
                $filtered->addItem($objEvent);
            } elseif ($objEvent->getSubType()==$typeEvent) {
                $filtered->addItem($objEvent);
            } else {
                // Ne rien faire
            }
            $this->next();
        }
        return $filtered;
    }

    public function sum(): int
    {
        $sum = 0;
        $this->rewind();
        while ($this->valid()) {
            $objEvent = $this->current();
            $sum += $objEvent->getQuantity();
            $this->next();
        }
        return $sum;
    }

    public function filterPitStop(bool $longStop, bool $failedShortStop=false): EventCollection
    {
        $filtered = new EventCollection();
        $this->rewind();
        while ($this->valid()) {
            $objEvent = $this->current();
            if ($objEvent->isLongStop()==$longStop && $objEvent->isFailedShortStop()==$failedShortStop) {
                $filtered->addItem($objEvent);
            }
            $this->next();
        }
        return $filtered;
    }

}
