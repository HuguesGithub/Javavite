<?php
namespace src\Collection;

class TestCollection extends Collection
{

    public function filterBy(string $typeTest): TestCollection
    {
        $filtered = new TestCollection();
        $this->rewind();
        while ($this->valid()) {
            $objTest = $this->current();
            if ($objTest::class==$typeTest) {
                $filtered->addItem($objTest);
            }
            $this->next();
        }
        return $filtered;
    }

    public function countScores(int $score): int
    {
        $nb = 0;
        $this->rewind();
        while ($this->valid()) {
            $objTest = $this->current();
            if ($objTest->getScore()==$score) {
                $nb++;
            }
            $this->next();
        }
        return $nb;
    }

    public function countFailItems(): int
    {
        $nb = 0;
        $this->rewind();
        while ($this->valid()) {
            $objTest = $this->current();
            if ($objTest->isFail()) {
                $nb++;
            }
            $this->next();
        }
        return $nb;
    }

    public function countForcedItems(): int
    {
        $nb = 0;
        $this->rewind();
        while ($this->valid()) {
            $objTest = $this->current();
            if ($objTest->isInflicted()) {
                $nb++;
            }
            $this->next();
        }
        return $nb;
    }

}
