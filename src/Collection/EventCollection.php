<?php
namespace src\Collection;

class EventCollection extends Collection
{
    public function getClassEvent(string $typeEvent): EventCollection
    {
        $filtered = new EventCollection();
        $this->rewind();
        while ($this->valid()) {
            $objEvent = $this->current();
            if ($objEvent::class==$typeEvent) {
                $filtered->addItem($objEvent);
            }
            $this->next();
        }
        return $filtered;
    }

    // Gear : ['gear'=>$gear, 'score'=>$score]
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

}
