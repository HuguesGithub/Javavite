<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\BodyTest;
use src\Entity\EngineTest;
use src\Entity\Player;
use src\Entity\SuspensionTest;

class PlayerController extends UtilitiesController
{
    private Player $objPlayer;

    public function __construct(Player $objPlayer)
    {
        $this->objPlayer = $objPlayer;
    }

    public function getRowStanding(): string
    {
        return $this->getRow([
            $this->objPlayer->getPlayerName(),
            $this->objPlayer->getEndPosition(),
            $this->objPlayer->getStartPosition(),
            $this->objPlayer->getMoves(),
            $this->objPlayer->getDnf()
        ]);

    }

    public function display(): string
    {
        $titreCard  = $this->objPlayer->getCardTitle();

        $content = '';
        $content .= StartController::displayPlayerStart($this->objPlayer);
        $content .= TireController::displayPlayerTires($this->objPlayer);
        $content .= FuelController::displayPlayerFuel($this->objPlayer);
        $content .= PitStopController::displayPlayerPitStop($this->objPlayer);
        $content .= BrakeController::displayPlayerBrake($this->objPlayer);
        $content .= TrailController::displayPlayerTrail($this->objPlayer);
        $content .= TaqController::displayPlayerTaQ($this->objPlayer);

        $bodyThrown = '';
        for ($i=1; $i<=5; $i++) {
            $bodyThrown .= $this->getRow([
                $i,
                $this->objPlayer->getTestCollection()->countScores($i),
                $i+5,
                $this->objPlayer->getTestCollection()->countScores($i+5),
                $i+10,
                $this->objPlayer->getTestCollection()->countScores($i+10),
                $i+15,
                $this->objPlayer->getTestCollection()->countScores($i+15),
            ]);
        }


        // Global
        $bodyContent = $this->getRow([
            LabelConstant::LBL_GLOBAL,
            $this->objPlayer->getTestCollection()->length(),
            $this->objPlayer->getTestCollection()->countFailItems(),
            $this->objPlayer->getTestCollection()->countForcedItems(),
        ]);
        // Moteur
        $bodyContent .= $this->getRow([
            LabelConstant::LBL_ENGINE,
            $this->objPlayer->getTestCollection()->filterBy(EngineTest::class)->length(),
            $this->objPlayer->getTestCollection()->filterBy(EngineTest::class)->countFailItems(),
            $this->objPlayer->getTestCollection()->filterBy(EngineTest::class)->countForcedItems(),
        ]);
        // Carrosserie
        $bodyContent .= $this->getRow([
            LabelConstant::LBL_BODY,
            $this->objPlayer->getTestCollection()->filterBy(BodyTest::class)->length(),
            $this->objPlayer->getTestCollection()->filterBy(BodyTest::class)->countFailItems(),
            $this->objPlayer->getTestCollection()->filterBy(BodyTest::class)->countForcedItems(),
        ]);
        // Tenue de route
        $bodyContent .= $this->getRow([
            LabelConstant::LBL_SUSPENSION,
            $this->objPlayer->getTestCollection()->filterBy(SuspensionTest::class)->length(),
            $this->objPlayer->getTestCollection()->filterBy(SuspensionTest::class)->countFailItems(),
            '-',
        ]);

        $attributes = [
            $titreCard,
            $content,
            $this->getRow([
                ConstantConstant::CST_NBSP,
                LabelConstant::LBL_THROWN_DICE,
                LabelConstant::LBL_FAILED_DICE,
                LabelConstant::LBL_FORCED_DICE,],
                false
            ),
            $bodyContent,
            $this->getRow([
                LabelConstant::LBL_THROW,
                LabelConstant::LBL_QUANTITY,
                LabelConstant::LBL_THROW,
                LabelConstant::LBL_QUANTITY,
                LabelConstant::LBL_THROW,
                LabelConstant::LBL_QUANTITY,
                LabelConstant::LBL_THROW,
                LabelConstant::LBL_QUANTITY],
                false),
            $bodyThrown
        ];
        $attributes = array_merge($attributes, $this->displayGears());
        return $this->getRender(TemplateConstant::TPL_CARD_PLAYER, $attributes);
    }

    private function displayGears(): array
    {
        $arr = [
            1 => ['min'=>1, 'max'=>2],
            2 => ['min'=>2, 'max'=>4],
            3 => ['min'=>4, 'max'=>8],
            4 => ['min'=>7, 'max'=>12],
            5 => ['min'=>11, 'max'=>20],
            6 => ['min'=>21, 'max'=>30]
        ];
        $content = '';

        foreach ($arr as $key => $minMax) {
            $min = $minMax['min'];
            $max = $minMax['max'];

            $arrContent = [];
            $styles = [];
            if ($min!=1) {
                $arrContent[] = ConstantConstant::CST_NBSP;
                $styles[] = ' colspan="'.($min-1).'"';
            }
            for ($i=$min; $i<=$max; $i++) {
                $arrContent[] = $this->objPlayer->getGearCollection()->countItems($key, $i);
                $styles[] = ' class="bg-g'.$key.'"';
            }
            if ($max!=30) {
                $arrContent[] = ConstantConstant::CST_NBSP;
                $styles[] = ' colspan="'.(30-$max).'"';
            }
            $content .= $this->getRow($arrContent, true, $styles);
        }

        return [
            $this->getRow(range(1, 30), false),
            $content
        ];
    }

}
