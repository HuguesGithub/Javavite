<?php
namespace src\Entity;

use src\Collection\EventCollection;
use src\Collection\PlayerCollection;
use src\Constant\ConstantConstant;
use src\Constant\TemplateConstant;
use src\Controller\GameController;

class Game extends Entity
{
    private PlayerCollection $playerCollection;
    protected EventCollection $eventCollection;
    protected array $events;
    private string $failTest;
    private bool $ignoreMove;
    private Player $activePlayer;
    
    public function __construct()
    {
        $this->failTest = '';
        $this->ignoreMove = false;
        $this->init();
    }

    private function init(): void
    {
        $this->playerCollection = new PlayerCollection();
        $this->eventCollection = new EventCollection();
        $this->activePlayer = new Player('');
    }

    public function getController(): GameController
    {
        return new GameController($this);
    }

    public function getPlayerCollection(): PlayerCollection
    {
        return $this->playerCollection;
    }

    public function getEventCollection(): EventCollection
    {
        return $this->eventCollection;
    }

    public function getActivePlayer(): Player
    {
        return $this->activePlayer;
    }

    // Ajout d'un Player à la partie
    public function addPlayer(string $playerName, int $startPosition=-1): void
    {
        $this->playerCollection->addItem(new Player($playerName, $startPosition));
    }

    // On récupère un joueur par son nom
    public function getPlayerByPlayerName(string $playerName): ?Player
    {
        return $this->playerCollection->getPlayerByName($playerName);
    }

    public function addGameTest(array $params): void
    {
        $objPlayer = $this->getPlayerByPlayerName($params[1]);
        $typeTest = $params[2];

        switch ($typeTest) {
            case 'moteur' :
            case 'Moteur' :
                $this->addGameEvent(
                    $objPlayer,
                    new EngineTest($params[3], $params[4]));
            break;
            case 'de tenue de Route' :
            case 'Tenue de route' :
                $this->addGameEvent(
                    $objPlayer,
                    new SuspensionTest($params[3], $params[4]));
            break;
            case 'Carrosserie' :
                $this->addGameEvent(
                    $objPlayer,
                    new BodyTest($params[3], $params[4]));
            break;
            case 'Départ' :
                $this->addGameEvent(
                    $objPlayer,
                    new StartTest($params[3]));
            break;
            default :
                echo 'Test ['.$typeTest.'] non couvert.<br>';
            break;
        }
    }

    public function setFinalPosition(Player $objPlayer, int $finalPosition=-1): void
    {
        $objPlayer->setEndPosition($finalPosition);
    }

    public function setIgnoreMove(): void
    {
        $this->ignoreMove = true;
    }
    

    public function cancelBrake(array $params): void
    {
        $playerName = $params[0];
        $typeBrake = $params[1];
        $indexPlayer = $this->indexPlayers[$playerName];
        if ($indexPlayer=='') {
            return;
        }
        $objPlayer = $this->objPlayers[$indexPlayer];

        $this->events[ConstantConstant::CST_BRAKE][ConstantConstant::CST_QUANTITY]--;
        $this->events[ConstantConstant::CST_BRAKE][$typeBrake]--;

        //$objPlayer->addPlayerEvent(ConstantConstant::CST_BRAKE, $typeBrake, -1);
    }

    public function addGameEvent(Player $objPlayer, Event $objEvent): void
    {
        if ($objPlayer==null) {
            return;
        }
        switch ($objEvent::class) {
            case DnfEvent::class :
                if ($objEvent->getType()=='') {
                    $objEvent->setType($this->failTest);
                }
            break;
            case BodyTest::class :
                $objEvent->setInflicted(!$this->activePlayer->isEqual($objPlayer));
                $this->failTest = ConstantConstant::CST_BODY;
            break;
            case EngineTest::class:
                $objEvent->setInflicted(!$this->activePlayer->isEqual($objPlayer));
                $this->failTest = ConstantConstant::CST_ENGINE;
            break;
            case SuspensionTest::class :
                $this->failTest = ConstantConstant::CST_SUSPENSION;
            break;
            case FuelEvent::class :
                if ($objEvent->getType()!=ConstantConstant::CST_1GEAR) {
                    $this->addGameEvent(
                        $objPlayer,
                        new BrakeEvent([ConstantConstant::CST_FUEL, 1])
                    );
                    if ($objEvent->getType()==ConstantConstant::CST_3GEAR) {
                        // TODO : Finaliser l'ajout de l'EngineEvent.
                        //$this->addGameEvent($objPlayer, new EngineEvent([ConstantConstant::CST_FUEL, 1]));
                    }
                }
            break;
            default :
            // Do nothing
            break;
        }
        $this->eventCollection->addItem($objEvent);
        $objPlayer->addPlayerEvent($objEvent);
    }
    
}
