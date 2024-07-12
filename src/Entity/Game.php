<?php
namespace src\Entity;

use src\Collection\EventCollection;
use src\Collection\PlayerCollection;
use src\Collection\TestCollection;
use src\Constant\ConstantConstant;
use src\Constant\TemplateConstant;
use src\Controller\GameController;

class Game extends Entity
{
    private PlayerCollection $playerCollection;
    protected TestCollection $testCollection;
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
        $this->testCollection = new TestCollection();
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

    public function getTestCollection(): TestCollection
    {
        return $this->testCollection;
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

    public function addTest(array $params): void
    {
        $objPlayer = $this->getPlayerByPlayerName($params[1]);
        $typeTest = $params[2];

        switch ($typeTest) {
            case 'moteur' :
            case 'Moteur' :
                $this->addGameTest(
                    $objPlayer,
                    new EngineTest($params[3], $params[4]));
            break;
            case 'de tenue de Route' :
            case 'Tenue de route' :
                $this->addGameTest(
                    $objPlayer,
                    new SuspensionTest($params[3], $params[4]));
            break;
            case 'Carrosserie' :
                $this->addGameTest(
                    $objPlayer,
                    new BodyTest($params[3], $params[4]));
            break;
            case 'Départ' :
                $this->addGameTest(
                    $objPlayer,
                    new StartTest($params[3]));
            break;
            default :
                echo 'Test ['.$typeTest.'] non couvert.<br>';
            break;
        }
    }

    public function addGameTest(Player $objPlayer, Test $objTest): void
    {
        if ($objTest::class==BodyTest::class) {
            $objTest->setInflicted(!$this->activePlayer->isEqual($objPlayer));
            $this->failTest = ConstantConstant::CST_BODY;
        } elseif ($objTest::class==EngineTest::class) {
            $objTest->setInflicted(!$this->activePlayer->isEqual($objPlayer));
            $this->failTest = ConstantConstant::CST_ENGINE;
        } elseif ($objTest::class==SuspensionTest::class) {
            $this->failTest = ConstantConstant::CST_SUSPENSION;
        } else {
            // Ne rien faire
        }
        $this->testCollection->addItem($objTest);
        $objPlayer->addPlayerTest($objTest);
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
        // En cas d'abandon, spécifier le type d'abandon
        if ($objEvent::class==ConstantConstant::CST_DNF && $objEvent->getType()=='') {
            $objEvent->setType($this->failTest);
        }
        // En cas de rétrogradation d'au moins 2 rapports, supprimer un Frein, voire un Moteur
        if ($objEvent::class==ConstantConstant::CST_FUEL && $objEvent->getType()!=ConstantConstant::CST_1GEAR) {
            $this->addGameEvent($objPlayer, new BrakeEvent([ConstantConstant::CST_FUEL, 1]));
            if ($objEvent->getType()==ConstantConstant::CST_3GEAR) {
            // TODO : Finaliser l'ajout de l'EngineEvent.
            //$this->addGameEvent($objPlayer, new EngineEvent([ConstantConstant::CST_FUEL, 1]));
            }
        }

        $this->eventCollection->addItem($objEvent);
        $objPlayer->addPlayerEvent($objEvent);
    }
    
    public function addTestEngine(Player $objPlayer, int $score, string $requis): void
    {
        $seuil = substr($requis, 1);
        $this->failTest = ConstantConstant::CST_ENGINE;
        
        $this->tests[ConstantConstant::CST_GLOBAL][ConstantConstant::CST_QUANTITY]++;
        if (!isset($this->tests[ConstantConstant::CST_GLOBAL][ConstantConstant::CST_SCORE][$score])) {
            $this->tests[ConstantConstant::CST_GLOBAL][ConstantConstant::CST_SCORE][$score] = 0;
        }
        $this->tests[ConstantConstant::CST_GLOBAL][ConstantConstant::CST_SCORE][$score]++;
        if ($score<=$seuil) {
            $this->tests[ConstantConstant::CST_GLOBAL][ConstantConstant::CST_FAIL]++;
        }
        if (!$this->activePlayer->isEqual($objPlayer)) {
            $this->tests[ConstantConstant::CST_GLOBAL][ConstantConstant::CST_INFLICTED]++;
        }
        
        $this->tests[ConstantConstant::CST_ENGINE][ConstantConstant::CST_QUANTITY]++;
        if (!isset($this->tests[ConstantConstant::CST_ENGINE][ConstantConstant::CST_SCORE][$score])) {
            $this->tests[ConstantConstant::CST_ENGINE][ConstantConstant::CST_SCORE][$score] = 0;
        }
        $this->tests[ConstantConstant::CST_ENGINE][ConstantConstant::CST_SCORE][$score]++;
        if ($score<=$seuil) {
            $this->tests[ConstantConstant::CST_ENGINE][ConstantConstant::CST_FAIL]++;
        }
        if (!$this->activePlayer->isEqual($objPlayer)) {
            $this->tests[ConstantConstant::CST_ENGINE][ConstantConstant::CST_INFLICTED]++;
        }
        
        $objPlayer->addPlayerTest($this->activePlayer, ConstantConstant::CST_ENGINE, $score, $seuil);
    }

}
