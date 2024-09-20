<?php
namespace src\Collection;

use src\Constant\ConstantConstant;
use src\Entity\GearEvent;

class EventCollection extends Collection
{

    public function getClassEvent(string $typeEvent, bool $extends=false): EventCollection
    {
        $filtered = new EventCollection();
        $this->rewind();
        while ($this->valid()) {
            $objEvent = $this->current();
            if ($objEvent::class==$typeEvent || $extends && is_subclass_of($objEvent::class, $typeEvent)) {
                $filtered->addItem($objEvent);
            }
            $this->next();
        }
        return $filtered;
    }

    public function filter(array $params): EventCollection
    {
        $filtered = new EventCollection();
        $this->rewind();
        while ($this->valid()) {
            $objEvent = $this->current();
            $bln = true;
            foreach ($params as $key=>$value) {
                if ($objEvent->getField($key)!=$value) {
                    $bln = false;
                }
            }
            if ($bln) {
                $filtered->addItem($objEvent);
            }
            $this->next();
        }
        return $filtered;
    }

    public function notFilter(array $params): EventCollection
    {
        $filtered = new EventCollection();
        $this->rewind();
        while ($this->valid()) {
            $objEvent = $this->current();
            $bln = true;
            foreach ($params as $key=>$value) {
                if ($objEvent->getField($key)==$value) {
                    $bln = false;
                }
            }
            if ($bln) {
                $filtered->addItem($objEvent);
            }
            $this->next();
        }
        return $filtered;
    }

    public function sum(bool $isGearEventLike=false): int
    {
        $sum = 0;
        $this->rewind();
        while ($this->valid()) {
            $objEvent = $this->current();
            if ($objEvent::class==GearEvent::class || $isGearEventLike) {
                $sum += $objEvent->getScore();
            } else {
                $sum += $objEvent->getQuantity();
            }
            $this->next();
        }
        return $sum;
    }

}
