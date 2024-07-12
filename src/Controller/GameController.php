<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Game;

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
        $str .= $this->addSection([
            $this->addSection([
                // Card Classement
                StandingsController::displayStandings($this->objGame),
                $this->addW100(),
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
            ),
            $this->addSection([
                // Card Global
                $this->displayGlobal(),
                // Card Moteur
                EngineController::displayEngine($this->objGame),
                $this->addW100(),
                // Card Carrosserie
                BodyController::displayBody($this->objGame),
                // Card Tenue de Route
                SuspensionController::displaySuspension($this->objGame)
                ],
                'col'
            ),
            $this->addW100(),
            // Card Vitesses
            GearController::displayGears($this->objGame)
            ],
            'p-3 mt-5'
        );

        // Joueurs
        $contentPlayers = [];
        $objPlayers = $this->objGame->getPlayerCollection();
        $objPlayers->rewind();
        $cpt = 1;
        while ($objPlayers->valid()) {
            $objPlayer = $objPlayers->current();
            $contentPlayers[] = $objPlayer->getController()->display();
            $objPlayers->next();
            if ($cpt%2==0) {
                $contentPlayers[] = $this->addW100();
            }
            $cpt++;
        }
        return $str.$this->addSection($contentPlayers, 'p-3 mt-5');
    }

    private function displayGlobal(): string
    {
        $content = '';
        for ($i=1; $i<=10; $i++) {
            $content .= $this->getRow([
                $i,
                $this->objGame->getTestCollection()->countScores($i),
                $i+10,
                $this->objGame->getTestCollection()->countScores($i+10)
            ]);
        }

        $attributes = [
            LabelConstant::LBL_GLOBAL,
            $this->getRow([
                LabelConstant::LBL_THROWN_DICE,
                LabelConstant::LBL_FAILED_DICE,
                LabelConstant::LBL_FORCED_DICE],
                false
            ),
            $this->getRow([
                $this->objGame->getTestCollection()->length(),
                $this->objGame->getTestCollection()->countFailItems(),
                $this->objGame->getTestCollection()->countForcedItems()],
                false),
            $this->getRow([
                LabelConstant::LBL_THROW,
                LabelConstant::LBL_QUANTITY,
                LabelConstant::LBL_THROW,
                LabelConstant::LBL_QUANTITY],
                false),
            $content
        ];
        return $this->getRender(TemplateConstant::TPL_CARD_DOUBLE_TABLE, $attributes);
    }

    private function getTopBar(): string
    {
        return '<div class="fixed-top bg-secondary p-2"><a href="/" class="btn btn-light btn-sm"><i class="fa-solid fa-angles-left"></i> Retour</a></div>';
    }
}
