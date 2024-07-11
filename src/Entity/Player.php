<?php
namespace src\Entity;

use src\Collection\EventCollection;
use src\Collection\GearCollection;
use src\Collection\TestCollection;
use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Controller\PlayerController;

class Player extends Entity
{
    private string $playerName;
    private int $moves;
    protected TestCollection $testCollection;
    protected GearCollection $gearCollection;
    protected EventCollection $eventCollection;
    private int $startPosition;
    private int $endPosition;
    
    public function __construct(string $playerName, int $startPosition=-1)
    {
        $this->playerName = $playerName;
        $this->moves = 0;
        $this->startPosition = $startPosition;
        $this->endPosition = 10;
        $this->init();
    }

    private function init(): void
    {
        $this->testCollection = new TestCollection();
        $this->gearCollection = new GearCollection();
        $this->eventCollection = new EventCollection();
    }

    public function getValue(string $tab, mixed $first='', string $second='', int $third=-1): mixed
    {
        $returned = '';
        if ($third!=-1) {
            $returned = $this->{$tab}[$first][$second][$third] ?? 0;
        } elseif ($second!='') {
            $returned = $this->{$tab}[$first][$second];
        } elseif ($first!='') {
            $returned = $this->{$tab}[$first];
        } else {
            $returned = $this->{$tab};
        }
        return $returned;
    }
    
    public function getCardTitle(): string
    {
        $str  = $this->playerName;
        $str .= ' - Arrivée : '.$this->endPosition.($this->endPosition==1 ? 'ère' : 'ème');
        $str .= ' - Départ : '.$this->startPosition.($this->startPosition==1 ? 'ère' : 'ème');
        $str .= ' - '.$this->getMoves().' coups';
        $dnf = $this->getDnf();
        if ($dnf!='') {
            $str .= ' - <span class="badge bg-danger">'.$dnf.'</span>';
        }
        return $str;
    }

    public function getPlayerName(): string
    {
        return $this->playerName;
    }

    public function getEndPosition(): int
    {
        return $this->endPosition;
    }

    public function getStartPosition(): int
    {
        return $this->startPosition;
    }

    public function getDnf(): string
    {
        $dnfCollection = $this->eventCollection->filterBy(DnfEvent::class);
        if ($dnfCollection->length()==0) {
            $str = '';
        } else {
            $dnfEvent = $dnfCollection->current();
            if ($dnfEvent->getType()==ConstantConstant::CST_BODY) {
                $str = LabelConstant::LBL_BODY;
            } elseif ($dnfEvent->getType()==ConstantConstant::CST_TIRE) {
                $str = LabelConstant::LBL_LONG_CURVE_EXIT;
            } else {
                $str = 'Inconnue';
            }
        }
        /*
            if ($this->events[ConstantConstant::CST_DNF][ConstantConstant::CST_ENGINE]==1) {
                $str = LabelConstant::LBL_ENGINE;
            } elseif ($this->events[ConstantConstant::CST_DNF][ConstantConstant::CST_BLOCKED]==1) {
                $str = LabelConstant::LBL_BLOCKED;
            } elseif ($this->events[ConstantConstant::CST_DNF][ConstantConstant::CST_SUSPENSION]==1) {
                $str = LabelConstant::LBL_SUSPENSION;
            }
            */
        return $str;
    }

    public function getController(): PlayerController
    {
        return new PlayerController($this);
    }

    public function getTestCollection(): TestCollection
    {
        return $this->testCollection;
    }

    public function getGearCollection(): GearCollection
    {
        return $this->gearCollection;
    }

    public function getEventCollection(): EventCollection
    {
        return $this->eventCollection;
    }
    
    public function addPlayerTest(Test $objTest): void
    {
        $this->testCollection->addItem($objTest);
    }






    public function getMoves(): int
    {
        return $this->gearCollection->length();
    }
    
    public function setEndPosition(int $endPosition): void
    {
        $this->endPosition = $endPosition;
    }

    public function isEqual(Player $objPlayer): bool
    {
        $blnOk = true;
        if ($this->playerName != $objPlayer->getPlayerName()) {
            $blnOk = false;
        }
        return $blnOk;
    }

    // Mutualisation des méthodes qui ajoutent un événement
    public function addPlayerEvent(Event $objEvent): void
    {
        // Si la quantité est -1, on va retirer le dernier élément de la Collection
        if ($objEvent->getQuantity()==-1) {
            $this->eventCollection->deleteLastItem();
        } else {
            // Si l'event est un abandon, on récupère le type pour le renseigner dans l'objet
            if ($objEvent::class==DnfEvent::class) {
                $this->endPosition = $objEvent->getDnfPosition();
            }
            $this->eventCollection->addItem($objEvent);
        }
    }

    public function addGear(Gear $objGear): void
    {
        $this->gearCollection->addItem($objGear);
        $this->moves++;
    }
    
    public function addTestTdr(Player $objPlayer, int $score, int $seuil): void
    {
        $this->tests[ConstantConstant::CST_GLOBAL][ConstantConstant::CST_QUANTITY]++;
        if (!isset($this->tests[ConstantConstant::CST_GLOBAL][ConstantConstant::CST_SCORE][$score])) {
            $this->tests[ConstantConstant::CST_GLOBAL][ConstantConstant::CST_SCORE][$score] = 0;
        }

        $this->tests[ConstantConstant::CST_SUSPENSION][ConstantConstant::CST_QUANTITY]++;
        if (!isset($this->tests[ConstantConstant::CST_SUSPENSION][ConstantConstant::CST_SCORE][$score])) {
            $this->tests[ConstantConstant::CST_SUSPENSION][ConstantConstant::CST_SCORE][$score] = 0;
        }
        $this->tests[ConstantConstant::CST_SUSPENSION][ConstantConstant::CST_SCORE][$score]++;
        if ($score<=$seuil) {
            $this->tests[ConstantConstant::CST_GLOBAL][ConstantConstant::CST_FAIL]++;
            $this->tests[ConstantConstant::CST_SUSPENSION][ConstantConstant::CST_FAIL]++;
        }
    }

}
