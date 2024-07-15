<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Game;
use src\Entity\PitStopEvent;
use src\Entity\PitStopTest;
use src\Entity\Player;

class PitStopController extends GameController
{
    public static function displayPitStops(Game $objGame): string
    {
        $controller = new PitStopController($objGame);

        $pitStopEventCollection = $objGame->getEventCollection()->getClassEvent(PitStopTest::class);

        $attributes = [
            LabelConstant::LBL_PITS,
            // class additionnelle pour card-body
            'p-0',
            $controller->getRow([
                LabelConstant::LBL_PIT_STOP,
                LabelConstant::LBL_LONG_PIT_STOP,
                LabelConstant::LBL_SUCCESS_SHORT_PIT_STOP,
                LabelConstant::LBL_FAIL_SHORT_PIT_STOP],
                false
            ),
            $controller->getRow([
                $pitStopEventCollection->length(),
                $pitStopEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_LONG_STOP])->length(),
                $pitStopEventCollection->filter([
                    ConstantConstant::CST_TYPE=>ConstantConstant::CST_SHORT_STOP,
                    ConstantConstant::CST_FAIL=>false])->length(),
                $pitStopEventCollection->filter([
                    ConstantConstant::CST_TYPE=>ConstantConstant::CST_SHORT_STOP,
                    ConstantConstant::CST_FAIL=>true])->length(),
            ])
        ];
        return $controller->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }

    public static function displayPlayerPitStop(Player $objPlayer): string
    {
        $pitStopEventCollection = $objPlayer->getEventCollection()->getClassEvent(PitStopEvent::class);

        return '<br>Stands : <span class="badge bg-info" title="Nombre d\'arrêts">'
            .$pitStopEventCollection->length()
            .'</span> - <span class="badge bg-success" title="Rapides réussis">'
            .$pitStopEventCollection->filter([
                ConstantConstant::CST_TYPE=>ConstantConstant::CST_SHORT_STOP,
                ConstantConstant::CST_FAIL=>false])->length()
            .'</span> - <span class="badge bg-danger" title ="Rapides échoués">'
            .$pitStopEventCollection->filter([
                ConstantConstant::CST_TYPE=>ConstantConstant::CST_SHORT_STOP,
                ConstantConstant::CST_FAIL=>true])->length()
            .'</span>';
    }
}
