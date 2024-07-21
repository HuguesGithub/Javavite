<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Game;
use src\Entity\BodyTest;
use src\Entity\EngineTest;
use src\Entity\MeteoTest;
use src\Entity\PitStopTest;
use src\Entity\StartTest;
use src\Entity\SuspensionTest;
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
        $playerSelection = SessionUtils::fromGet('player');
        if ($playerSelection=='') {
            $str .= $this->addSection([
                $this->addSection([
                    // Card Classement
                    StandingsController::displayStandings($this->objGame),
                    $this->addW100(),
                    // Card Global
                    $this->displayGlobal(),
                    // Card Moteur
                    EngineController::displayEngine($this->objGame),
                    $this->addW100(),
                    // Card Carrosserie
                    BodyController::displayBody($this->objGame),
                    // Card Tenue de Route
                    SuspensionController::displaySuspension($this->objGame),
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
                $this->addW100(),
                $this->addSection([
                    // Card Vitesses
                    GearController::displayGears($this->objGame)
                    ],
                    'col'
                ),
                ],
                'p-3 mt-5 col-8 offset-2'
            );
        }

        $lis = '';
        // Joueurs
        $contentPlayers = [];
        $objPlayers = $this->objGame->getPlayerCollection();
        $objPlayers->rewind();
        while ($objPlayers->valid()) {
            $objPlayer = $objPlayers->current();
            $playerName = $objPlayer->getPlayerName();
            if ($playerSelection!='' && $playerSelection!=$playerName) {
                $objPlayers->next();
                continue;
            }
            // Construction du contenu de la section principale
            $contentPlayers[] = $this->addSection(
                [$objPlayer->getController()->display()],
                'col'
            );
            $contentPlayers[] = $this->addW100();
            // Construction du menu latéral
            $lis .= '<li class="list-group-item"><a href="#'.$playerName.'">'.$playerName.'</a></li>';
            $objPlayers->next();
        }

        // Menu flottant
        $menuFlottant = '<div class="card grey-panel">
            <div class="card-header grey-header text-center">
                <h5>Accès rapide</h5>
            </div>
            <div class="card-body">
                <ul class="list-group-items ps-0">
                    '.$lis.'
                </ul>
            </div>
        </div>';
/*
        $fileName = 'test.html';
        $handle = fopen(PLUGIN_PATH.TemplateConstant::HTML_PATH.$fileName, 'w');
        fputs($handle, $str.$this->addSection($contentPlayers, 'p-3 mt-3 col-8 offset-2'));
        fclose($handle);
*/
        return $str.$this->addSection($contentPlayers, 'p-3 mt-3 col-8 offset-2').$menuFlottant;
    }

    private function displayGlobal(): string
    {
        $arrTestClasses = [
            EngineTest::class,
            BodyTest::class,
            MeteoTest::class,
            PitStopTest::class,
            StartTest::class,
            SuspensionTest::class
        ];

        $quantity = 0;
        $quantityFail = 0;
        $quantityInflicted = 0;
        $scores = [];
        foreach ($arrTestClasses as $objTest) {
            $classCollection = $this->objGame->getEventCollection()->getClassEvent($objTest);
            if ($objTest==PitStopTest::class) {
                // Pour les PitStop, on doit ne prendre en compte que les arrêts courts.
                // Les arrêts longs sont listés mais ne comptent pas comme des tests à proprement parlé
                $quantity += $classCollection
                    ->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_SHORT_STOP])
                    ->length();
            } else {
                $quantity += $classCollection->length();
            }
            $quantityFail += $classCollection->filter([ConstantConstant::CST_FAIL=>true])->length();
            $quantityInflicted += $classCollection->filter([ConstantConstant::CST_INFLICTED=>true])->length();

            for ($i=1; $i<=20; $i++) {
                if (!isset($scores[$i])) {
                    $scores[$i] = 0;
                }
                $scores[$i] += $classCollection->filter([ConstantConstant::CST_SCORE=>$i])->length();
            }
        }

        $content = '';
        for ($i=1; $i<=10; $i++) {
            $content .= $this->getRow([
                $i,
                $scores[$i],
                $i+10,
                $scores[$i+10]
            ],
            true,
            [' class="bg-light"', '', ' class="bg-light"', '']);
        }

        $style = ' class="bg-dark text-white"';
        $attributes = [
            LabelConstant::LBL_GLOBAL,
            $this->getRow([
                LabelConstant::LBL_THROWN_DICE,
                LabelConstant::LBL_FAILED_DICE,
                LabelConstant::LBL_FORCED_DICE],
                false,
                array_fill(0, 3, $style)
            ),
            $this->getRow([
                $quantity,
                $quantityFail,
                $quantityInflicted],
                false),
            $this->getRow([
                LabelConstant::LBL_THROW,
                LabelConstant::LBL_QUANTITY,
                LabelConstant::LBL_THROW,
                LabelConstant::LBL_QUANTITY],
                false,
                array_fill(0, 4, $style)),
            $content
        ];
        return $this->getRender(TemplateConstant::TPL_CARD_DOUBLE_TABLE, $attributes);
    }

    private function getTopBar(): string
    {
        return '<div class="fixed-top bg-secondary p-2"><a href="/" class="btn btn-light btn-sm"><i class="fa-solid fa-angles-left"></i> Retour</a></div>';
    }
}
