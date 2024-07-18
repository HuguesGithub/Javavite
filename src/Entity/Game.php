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
    private string $failTest;
    private bool $ignoreMove;
    private Player $activePlayer;
    
    public function __construct()
    {
        $this->failTest = '';
        $this->ignoreMove = false;
        $this->init();
    }

    public function __toString(): string
    {
        $str  = parent::__construct();
        $str .= ConstantConstant::CST_TAB."Liste des joueurs :".ConstantConstant::CST_EOL;
        $str .= $this->playerCollection->__toString();
        $str .= ConstantConstant::CST_TAB."Liste des événements :".ConstantConstant::CST_EOL;
        $str .= $this->eventCollection->__toString();
        $str .= ConstantConstant::CST_TAB."failTest : ".$this->failTest.ConstantConstant::CST_EOL;
        $str .= ConstantConstant::CST_TAB."ignoreMove :".($this->ignoreMove?'true':'false').ConstantConstant::CST_EOL;
        $str .= ConstantConstant::CST_TAB."activePlayer :".$this->activePlayer->getPlayerName().ConstantConstant::CST_EOL;
        return $str;
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

    public function setActivePlayer(Player $objPlayer): void
    {
        $this->activePlayer = $objPlayer;
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
                $this->addGameEvent(
                    $objPlayer,
                    new EngineTest($params[3], $params[5]));
            break;
            case 'de tenue de Route' :
                $this->addGameEvent(
                    $objPlayer,
                    new SuspensionTest($params[3], $params[5]));
            break;
            case 'carrosserie' :
                $this->addGameEvent(
                    $objPlayer,
                    new BodyTest($params[3], $params[5]));
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

    public function addGameEvent(Player &$objPlayer, Event $objEvent): void
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
                    // On a loggué un frein en mode Frein juste avant, il faut l'enlever
                    //$this->eventCollection->deleteLast();
                    //$objPlayer->removePlayerEvent();
                    // TODO : Si on est dans le cas du joueur qui hoste, il ne faudrait pas remove le dernier event du joueur :/
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
            case GearEvent::class :
                // Dans le cas d'une 4è, 5è ou 6è dont le déplacement est de 4 ou moins, on est probablement dans le cas d'une aspiration.
                // Alors, on ne l'enregistre pas.
                if ($objEvent->getType()>=4 && $objEvent->getScore()<=4) {
                    return;
                }
            break;
            case TireEvent::class :
                //var_dump($objEvent);
            break;
            default :
            // Do nothing
            break;
        }
        $this->eventCollection->addItem($objEvent);
        $objPlayer->addPlayerEvent($objEvent);
    }
    
}
