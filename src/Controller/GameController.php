<?php
namespace src\Controller;

use src\Collection\EventCollection;
use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Game;
use src\Entity\BodyTest;
use src\Entity\EngineTest;
use src\Entity\GearEvent;
use src\Entity\MeteoTest;
use src\Entity\PitStopTest;
use src\Entity\StartTest;
use src\Entity\SuspensionTest;
use src\Entity\TestEvent;
use src\Utils\SessionUtils;

class GameController extends UtilitiesController
{
    private Game $objGame;

    public function __construct(Game $objGame)
    {
        $this->objGame = $objGame;
    }

    public function display(): string
    {
        $str = $this->getTopBar();
        $str .= '<div class="tab-content col-8 offset-2">';

        $eventCollection = $this->objGame->getEventCollection()->getClassEvent(TestEvent::class, true);
        $gearEventCollection = $this->objGame->getEventCollection()->getClassEvent(GearEvent::class);

        $playerSelection = SessionUtils::fromGet('player');
        if ($playerSelection=='') {
            $this->objGame->sortPlayers();
            $str .= $this->addNavTabs();
            // Card Classement
            $str .= $this->addTab(StandingsController::displayStandings($this->objGame), 'standings', true);
            // Card Jets de dés
            $str .= $this->addTab($this->diceThrow($eventCollection), 'dice');
            // Card Vitesses
            $str .= $this->addTab($this->gearGraph($gearEventCollection), 'speed');
            // Cards Divers
            $str .= $this->addTab($this->tabOthers(), 'other');
        }

        // Joueurs
        $objPlayers = $this->objGame->getPlayerCollection();
        $objPlayers->rewind();
        while ($objPlayers->valid()) {
            $objPlayer = $objPlayers->current();
            $playerName = $objPlayer->getPlayerName();
            if ($playerSelection!='' && $playerSelection!=$playerName) {
                $objPlayers->next();
                continue;
            }

            $str .= $this->addTab($objPlayer->getController()->display(), 'player'.$objPlayer->getEndPosition());
            $objPlayers->next();
        }
        $str .= '</div>';

        return $str;
    }

    private function getTopBar(): string
    {
        return '<div class="fixed-top bg-secondary p-2"><a href="/" class="btn btn-light btn-sm"><i class="fa-solid fa-angles-left"></i> Retour</a></div>';
    }

    private function addNavTabs(): string
    {
        $str  = '<ul class="nav nav-tabs justify-content-between pt-5" role="tabList">';
        $str .= '<li class="nav-item"><button class="nav-link active" id="standings-tab" data-bs-toggle="tab" data-bs-target="#standings" type="button" role="tab" aria-controls="standings" aria-selected="true">Classements</button></li>';
        $str .= '<li class="nav-item"><button class="nav-link" id="dice-tab" data-bs-toggle="tab" data-bs-target="#dice" type="button" role="tab" aria-controls="dice" aria-selected="true">Jets de dés</button></li>';
        $str .= '<li class="nav-item"><button class="nav-link" id="speed-tab" data-bs-toggle="tab" data-bs-target="#speed" type="button" role="tab" aria-controls="speed" aria-selected="true">Vitesses</button></li>';
        $str .= '<li class="nav-item"><button class="nav-link" id="other-tab" data-bs-toggle="tab" data-bs-target="#other" type="button" role="tab" aria-controls="other" aria-selected="true">Divers</button></li>';

        $str .= '<li class="nav-item dropdown">';
        $str .= '<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Joueurs</a>';
        $str .= '<ul class="dropdown-menu">';
        $objPlayers = $this->objGame->getPlayerCollection();
        $objPlayers->rewind();
        while ($objPlayers->valid()) {
            $objPlayer = $objPlayers->current();
            $playerName = $objPlayer->getPlayerName();
            $id = 'player'.$objPlayer->getEndPosition();
            $str .= '<li><a class="dropdown-item" id="'.$id.'-tab" data-bs-toggle="tab" data-bs-target="#'.$id.'" type="button" role="tab" aria-controls="'.$id.'" aria-selected="true">'.$playerName.'</a></li>';
            $objPlayers->next();
        }
        $str  .= '</ul>';
        $str  .= '</li>';
        $str  .= '</ul>';

        return $str;
    }

    private function addTab(string $content, string $id, bool $active=false): string
    {
        return '<div class="tab-pane'.($active?' active':'').'" id="'.$id.'" role="tabpanel" aria-labelledby="'.$id.'-tab">'.$content.'</div>';
    }

    private function diceThrow(EventCollection $eventCollection): string
    {
        $attributes = [
            LabelConstant::LBL_THROWN_DICE,
            // class additionnelle pour card-body
            'p-0',
            '',
            $this->getThrownDiceBlock($eventCollection)
        ];
        return $this->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }

    private function gearGraph(EventCollection $eventCollection): string
    {
        $attributes = [
            LabelConstant::LBL_MOVE_DICE,
            // class additionnelle pour card-body
            'p-0',
            '',
            GearController::displayGears($eventCollection)
        ];
        return $this->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }

    private function tabOthers(): string
    {
        return $this->addSection([
            // Card Départ
            StartController::displayStart($this->objGame),
            // Card Stands
            PitStopController::displayPitStops($this->objGame),
            $this->addW100(),
            // Card Abandon
            DnfController::displayDnfs($this->objGame),
            $this->addW100(),
            // Card Consommation
            FuelController::displayFuel($this->objGame),
            // Card Pneu
            TireController::displayTires($this->objGame),
            $this->addW100(),
            // Card Frein
            BrakeController::displayBrakes($this->objGame),
            $this->addW100(),
            // Card Aspiration
            TrailController::displayTrails($this->objGame),
            // Card Tête A Queue
            TaqController::displayTaQ($this->objGame)
            ],
            'col'
        );

    }
}
