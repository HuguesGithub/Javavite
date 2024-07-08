<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class LogFile extends Entity
{
    private array $lines;
    private array $tab;
    private bool $blnPitStop;
    private bool $blnTrail;
    private int $hostStartingPosition;
    
    private Game $objGame;

    public function __construct(string $fileName=null)
    {
        if ($fileName!=null) {
            $handle = fopen(PLUGIN_PATH.$fileName, 'r');
            if ($handle!==false) {
                while (!feof($handle)) {
                    $line = fgets($handle);
                    $this->lines[] = $line;
                }
                fclose($handle);
            }
        }
        $this->tab = [
            'players' => [],
        ];
        $this->blnPitStop = false;
        $this->blnTrail = false;
        $this->initGame();
    }

    private function initGame(): void
    {
        $this->objGame = new Game();
    }

    public function parse(): array
    {
        $cptStartPosition = 1;
        $dnfPosition = 0;
        $cptEndPosition = 1;
        $tempEvent = null;
        // Rappel : \d signifie [0-9]
        $patternChoixStand = '/(.*) choisi son stand/';
        $patternTestDepart = '/(.*) test Départ :(\d*)/';
        $patternMove = '/(.*) passe la (.*) et fait (\d*) au/';
        $patternTestBody = '/Test carrosserie pour (.*) : Jet = ([\d]*) {2}\(requis([<>\d]*)\)/';
        $patternDnf = '/(.*) est élimin/';
        $patternPneus = '/(.*) sort du virage en dérapant de {1,2}(\d+) .*pneus (.*)/';
        $patternDnf2 = '/(.*) est parti dans les graviers/';
        $patternWinner = '/(.*) remporte la course/';
        $patternFinish = '/(.*) franchit la ligne d/';
        $patternConso = '/(.*) rétrograde(.*)/';
        $patternPitStop = '/(.*) s.arrête aux stands/';
        $patternFrein = '/(.*) ecrase sa pédale de frein pour ne pas avancer trop/';
        $patternAspiration = '/(.*) peut profiter de l.aspiration sur (.*)/';
        $patternTest = '/(.*) : Test (.*) : Jet = (\d*).*requis ([<>\d]*)/';

        $patternFreinAnnul = '/(.*) choisit finalement de ne pas appuyer sur le frein/';
        $patternLateBrake = '/(.*) freine en entrée de virage suite à l\'aspiration/';
        $patternTeteAQueue = '/(.*) fait un tête à queue en sortie de virage/';

        $arrLignesNonTraitees = [];
        
        foreach ($this->lines as $line) {
            if ($this->excludeLines($line)) {
                continue;
            }

            if (preg_match($patternChoixStand, $line, $matches)) {
                $this->objGame->addPlayer($matches[1], $cptStartPosition);
                $cptStartPosition++;
                $dnfPosition++;
            } elseif (strpos($line, 'vous de choisir votre stand')!==false) {
                $this->objGame->addPlayer('unknown', $cptStartPosition);
                $this->hostStartingPosition = $cptStartPosition;
                $cptStartPosition++;
                $dnfPosition++;
            } elseif (preg_match($patternTestDepart, $line, $matches)) {
                $this->objGame->addGameTest(
                    $this->objGame->getPlayerByPlayerName($matches[1]),
                    new StartTest($matches[2]));
            } elseif (preg_match($patternMove, $line, $matches)) {
                $currentPlayer = $this->objGame->getPlayerByPlayerName($matches[1]);
                if ($this->blnTrail) {
                    $activePlayer = $this->objGame->getActivePlayer();
                    if ($activePlayer->isEqual($currentPlayer)) {
                        $tempEvent->setSubType(ConstantConstant::CST_ACCEPTED);
                        $this->objGame->addGameEvent($activePlayer, $tempEvent);
                    } else {
                        $tempEvent->setSubType(ConstantConstant::CST_DECLINED);
                        $this->objGame->addGameEvent($activePlayer, $tempEvent);
                    }
                    $this->blnTrail = false;
                    $tempEvent = null;
                }
                $this->objGame->addGear(
                    $currentPlayer,
                    new Gear((int)substr($matches[2], 0, 1), $matches[3]));
            } elseif (preg_match($patternTestBody, $line, $matches)) {
                $this->objGame->addGameTest(
                    $this->objGame->getPlayerByPlayerName($matches[1]),
                    new BodyTest($matches[2], $matches[3]));
            } elseif (preg_match($patternDnf, $line, $matches) || preg_match($patternDnf2, $line, $matches)) {
                $this->objGame->addGameEvent(
                    $this->objGame->getPlayerByPlayerName($matches[1]),
                    new DnfEvent([$dnfPosition]));
                $dnfPosition--;
            } elseif (preg_match($patternPneus, $line, $matches)) {
                $this->objGame->addGameEvent(
                    $this->objGame->getPlayerByPlayerName($matches[1]),
                    new TireEvent([ConstantConstant::CST_TIRE, $matches[2]]));
            } elseif (preg_match($patternWinner, $line, $matches) || preg_match($patternFinish, $line, $matches)) {
                $this->objGame->setFinalPosition(
                    $this->objGame->getPlayerByPlayerName($matches[1]),
                    $cptEndPosition);
                $cptEndPosition++;
            } elseif (preg_match($patternConso, $line, $matches)) {
                $this->objGame->addGameEvent(
                    $this->objGame->getPlayerByPlayerName($matches[1]),
                    new FuelEvent([$matches[2]]));
            } elseif ($this->blnPitStop || preg_match($patternPitStop, $line, $matches)) {
                $this->dealWithPitStop($line);
            } elseif (preg_match($patternFrein, $line, $matches)) {
                $this->objGame->addGameEvent(
                    $this->objGame->getPlayerByPlayerName($matches[1]),
                    new BrakeEvent([ConstantConstant::CST_BRAKE, 1]));
            } elseif (preg_match($patternAspiration, $line, $matches)) {
                $tempEvent = new TrailEvent([$this->objGame->getPlayerByPlayerName($matches[2])]);
                $this->blnTrail = true;
            } elseif (preg_match($patternTest, $line, $matches)) {
                $this->objGame->addTest($matches);
                                    /*
            } elseif (preg_match($patternLateBrake, $line, $matches)) {
                $this->objGame->addGameEvent(
                    $this->objGame->getPlayerByPlayerName($matches[1]),
                    new BrakeEvent([ConstantConstant::CST_TRAIL, 1]));
            } elseif (preg_match($patternFreinAnnul, $line, $matches)) {
                $this->objGame->cancelBrake([$matches[1], ConstantConstant::CST_BRAKE]);
            } elseif (preg_match($patternTeteAQueue, $line, $matches)) {
                $this->objGame->addGameEvent(
                    $this->objGame->getPlayerByPlayerName($matches[1]),
                    new TaqEvent());
                    */
            } else {
                // Ligne non traitée.
                if ($line!='') {
                    $arrLignesNonTraitees[] = $line;
                }
            }
        }
        return $arrLignesNonTraitees;
    }

    private function excludeLines(string $line): bool
    {
        $blnOk = false;
        // L'idée est de chercher certaines phrases clés qui sont à ignorer.
        // Ca permet d'éviter de passer dans toutes les regexp et de ne rien trouver.
        $checks = [
            "c'est votre tour!",
            "perd un morceau",
            "perd un point moteur",
            "perd en adhérence",
            "Bienvenue dans les essais",
            "Score = Nombre de Coups",
            "Le commissaire de course",
            "En cas de comportement",
            "La piste est",
            "est parti comme une fusée",
            "Essai n°",
            "La course commencera dès le choix",
            "Faites vrombir les moteurs",
            "de pénalité pour être sorti du virage",
            "Le temps est au beau fixe",
        ];
        foreach ($checks as $check) {
            if (strpos($line, $check)!==false) {
                return true;
            }
        }
        return $blnOk;
    }
    
    private function dealWithPitStop(string $line): void
    {
        $patternLongStop = '/(.*) choisit un arrêt long/';
        $patternShortStopFail = '/(.*) termine son tour à enguirlander les mécanos . \(jet = (\d*) /';
        $patternShortStopSuccess = '/(.*) repart immédiatement des stands . \(jet = (\d*) /';
        if (preg_match($patternLongStop, $line, $matches)) {
            // On ajoute un arrêt long.
            $this->objGame->addGameEvent(
                $this->objGame->getPlayerByPlayerName($matches[1]),
                new PitStopEvent(true));
            $this->blnPitStop = false;
        } elseif (preg_match($patternShortStopFail, $line, $matches)) {
            // 13champion93 termine son tour à enguirlander les mécanos ! (jet = 17 , requis <10)
            $this->objGame->addGameEvent(
                $this->objGame->getPlayerByPlayerName($matches[1]),
                new PitStopEvent(false, true));
            $this->objGame->addGameTest(
                $this->objGame->getPlayerByPlayerName($matches[1]),
                new PitStopTest($matches[2]));
                $this->blnPitStop = false;
        } elseif (preg_match($patternShortStopSuccess, $line, $matches)) {
            // Antho repart immédiatement des stands ! (jet = 1 , requis <10)
            $this->objGame->addGameEvent(
                $this->objGame->getPlayerByPlayerName($matches[1]),
                new PitStopEvent(false, false));
            $this->objGame->addGameTest(
                $this->objGame->getPlayerByPlayerName($matches[1]),
                new PitStopTest($matches[2]));
                $this->objGame->setIgnoreMove();
            $this->blnPitStop = false;
        } else {
            $this->blnPitStop = true;
        }
    }
    
    public function display(): string
    {
        return $this->objGame->getController()->display();
    }
}
