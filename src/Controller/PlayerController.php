<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Controller\GearController;
use src\Entity\BodyTest;
use src\Entity\EngineTest;
use src\Entity\GearEvent;
use src\Entity\TestEvent;
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
            $this->objPlayer->getDnf()],
            true,
            [ConstantConstant::CST_CLASS_BG_LIGHT, '', '', '', $this->objPlayer->getDnf()=='' ? '' : ' class="bg-danger"']
        );

    }

    public function display(): string
    {
        $eventCollection = $this->objPlayer->getEventCollection()->getClassEvent(TestEvent::class, true);
        $gearEventCollection = $this->objPlayer->getEventCollection()->getClassEvent(GearEvent::class);
        $titreCard  = $this->objPlayer->getCardTitle();

        $bodyThrownDice = $this->getThrownDiceBlock($eventCollection, false);
        $bodyGear = GearController::displayGears($gearEventCollection);

        $attributes = [
            $titreCard,
            '',//$content,
            '',
            $bodyThrownDice,
            '',
            $bodyGear,
            '',//$anchor,
            '',//$headerGear,
            '',//$contentGear,
            '',//$debug
        ];
        return $this->getRender(TemplateConstant::TPL_CARD_PLAYER, $attributes);


/*

        $debug = '';
        $events = $this->objPlayer->getEventCollection();
        $events->rewind();
        while ($events->valid()) {
            $event = $events->current();
            $debug .= EventController::getEventLi($event);
            $events->next();
        }
            
        $arrTestClasses = [
            EngineTest::class,
            BodyTest::class,
            PitStopTest::class,
            StartTest::class,
            SuspensionTest::class
        ];
        $quantity = 0;
        $quantityFail = 0;
        $quantityInflicted = 0;
        foreach ($arrTestClasses as $objTest) {
            $classCollection = $this->objPlayer->getEventCollection()->getClassEvent($objTest);
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
        }

        $anchor = $this->objPlayer->getPlayerName();

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
                $this->objPlayer->getEventCollection()->filter([ConstantConstant::CST_SCORE=>$i])->length(),
                $i+5,
                $this->objPlayer->getEventCollection()->filter([ConstantConstant::CST_SCORE=>$i+5])->length(),
                $i+10,
                $this->objPlayer->getEventCollection()->filter([ConstantConstant::CST_SCORE=>$i+10])->length(),
                $i+15,
                $this->objPlayer->getEventCollection()->filter([ConstantConstant::CST_SCORE=>$i+15])->length(),
            ]);
        }

        // Global
        $bodyContent = $this->getRow([
            LabelConstant::LBL_GLOBAL,
            $quantity,
            $quantityFail,
            $quantityInflicted
        ]);
        // Moteur
        $bodyContent .= $this->getRow([
            LabelConstant::LBL_ENGINE,
            $this->objPlayer->getEventCollection()->getClassEvent(EngineTest::class)->length(),
            $this->objPlayer->getEventCollection()
                ->getClassEvent(EngineTest::class)
                ->filter([ConstantConstant::CST_FAIL=>true])
                ->length(),
            $this->objPlayer->getEventCollection()
                ->getClassEvent(EngineTest::class)
                ->filter([ConstantConstant::CST_INFLICTED=>true])
                ->length(),
        ]);
        // Carrosserie
        $bodyContent .= $this->getRow([
            LabelConstant::LBL_BODY,
            $this->objPlayer->getEventCollection()->getClassEvent(BodyTest::class)->length(),
            $this->objPlayer->getEventCollection()
                ->getClassEvent(BodyTest::class)
                ->filter([ConstantConstant::CST_FAIL=>true])
                ->length(),
            $this->objPlayer->getEventCollection()
                ->getClassEvent(BodyTest::class)
                ->filter([ConstantConstant::CST_INFLICTED=>true])
                ->length(),
        ]);
        // Tenue de route
        $bodyContent .= $this->getRow([
            LabelConstant::LBL_SUSPENSION,
            $this->objPlayer->getEventCollection()->getClassEvent(SuspensionTest::class)->length(),
            $this->objPlayer->getEventCollection()
                ->getClassEvent(SuspensionTest::class)
                ->filter([ConstantConstant::CST_FAIL=>true])
                ->length(),
            '-',
        ]);

        $debug = '';
        $events = $this->objPlayer->getEventCollection();
        $events->rewind();
        while ($events->valid()) {
            $event = $events->current();
            $debug .= EventController::getEventLi($event);
            $events->next();
        }

        $headerGear = '';
        $contentGear = '';
        $this->displayGears($headerGear, $contentGear);
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
            $bodyThrown,
            $anchor,
            $headerGear,
            $contentGear,
            $debug
        ];

        return $this->getRender(TemplateConstant::TPL_CARD_PLAYER, $attributes);
        */
    }

    private function displayGears(string &$header, string &$content): void
    {
        $header = $this->getRow(range(1, 30), false);

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
                $arrContent[] = $this->objPlayer
                    ->getEventCollection()
                    ->getClassEvent(GearEvent::class)
                    ->filter([ConstantConstant::CST_TYPE=>$key, ConstantConstant::CST_SCORE=>$i])
                    ->length();
                $styles[] = ' class="bg-g'.$key.'"';
            }
            if ($max!=30) {
                $arrContent[] = ConstantConstant::CST_NBSP;
                $styles[] = ' colspan="'.(30-$max).'"';
            }
            $content .= $this->getRow($arrContent, true, $styles);
        }
    }

}
