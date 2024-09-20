<?php
namespace src\Entity;

use src\Collection\EventCollection;
use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Controller\PlayerController;

class Player extends Entity
{
    private string $playerName;
    protected EventCollection $eventCollection;
    private int $startPosition;
    private int $endPosition;
    
    public function __construct(string $playerName, int $startPosition=-1)
    {
        $this->playerName = $playerName;
        $this->startPosition = $startPosition;
        $this->endPosition = 10;
        $this->init();
    }

    public function __toString(): string
    {
        $str  = $this->playerName.ConstantConstant::CST_EOL;
        $str .= LabelConstant::LBL_START_POSITION." : ".$this->startPosition.ConstantConstant::CST_EOL;
        $str .= LabelConstant::LBL_FINISH_POSITION." : ".$this->endPosition.ConstantConstant::CST_EOL;
        $str .= 'Evénements :'.ConstantConstant::CST_EOL;
        return $str . $this->eventCollection->__toString();
    }

    private function init(): void
    {
        $this->eventCollection = new EventCollection();
    }
    
    public function getCardTitle(): string
    {
        $str  = $this->playerName;
        $str .= ' - Position à l\'arrivée : '.$this->endPosition.($this->endPosition==1 ? 'ère' : 'ème');
        $str .= ' - Position au départ : '.$this->startPosition.($this->startPosition==1 ? 'ère' : 'ème');
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
        $dnfCollection = $this->eventCollection->getClassEvent(DnfEvent::class);
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

    public function getEventCollection(): EventCollection
    {
        return $this->eventCollection;
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

    public function removePlayerEvent(): void
    {
        $this->eventCollection->deleteLast();
    }

    // Mutualisation des méthodes qui ajoutent un événement
    public function addPlayerEvent(Event $objEvent): void
    {
        // Si la quantité est -1, on va retirer le dernier élément de la Collection
        if ($objEvent->getQuantity()==-1) {
            $this->removePlayerEvent();
        } else {
            // Si l'event est un abandon, on récupère le type pour le renseigner dans l'objet
            if ($objEvent::class==DnfEvent::class) {
                $this->endPosition = $objEvent->getDnfPosition();
            }
            $this->eventCollection->addItem($objEvent);
        }
    }

}
