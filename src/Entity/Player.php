<?php
namespace src\Entity;

use src\Collection\EventCollection;
use src\Collection\TestCollection;
use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Controller\PlayerController;

class Player extends Entity
{
    private string $playerName;
    private int $moves;
    protected TestCollection $testCollection;
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
        $this->eventCollection = new EventCollection();
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
            } elseif ($dnfEvent->getType()==ConstantConstant::CST_ENGINE) {
                $str = LabelConstant::LBL_ENGINE;
            } elseif ($dnfEvent->getType()==ConstantConstant::CST_BLOCKED) {
                $str = LabelConstant::LBL_BLOCKED;
            } elseif ($dnfEvent->getType()==ConstantConstant::CST_SUSPENSION) {
                $str = LabelConstant::LBL_SUSPENSION;
            } else {
                $str = 'Inconnue';
            }
        }
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

    public function getEventCollection(): EventCollection
    {
        return $this->eventCollection;
    }
    
    public function addPlayerTest(TestEvent $objTest): void
    {
        $this->testCollection->addItem($objTest);
    }






    public function getMoves(): int
    {
        return $this->eventCollection->getClassEvent(GearEvent::class)->length();
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

}
