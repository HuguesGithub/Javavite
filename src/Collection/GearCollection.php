<?php
namespace src\Collection;

class GearCollection extends Collection
{
    // ['gear'=>$gear, 'score'=>$score]
    public function filterBy(array $params): GearCollection
    {
        $filtered = new GearCollection();
        $this->rewind();
        while ($this->valid()) {
            $objGear = $this->current();
            $bln = true;
            foreach ($params as $key=>$value) {
                if ($objGear->getField($key)!=$value) {
                    $bln = false;
                }
            }
            if ($bln) {
                $filtered->addItem($objGear);
            }
            $this->next();
        }
        return $filtered;
    }

}
