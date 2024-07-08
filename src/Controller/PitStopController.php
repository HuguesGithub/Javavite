<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Game;
use src\Entity\PitStopEvent;
use src\Entity\Player;

class PitStopController extends GameController
{
    public static function displayPitStops(Game $objGame): string
    {
        $controller = new PitStopController($objGame);

        $eventCollection = $objGame->getEventCollection();
        $pitStopEventCollection = $eventCollection->filterBy(PitStopEvent::class);

        $attributes = [
            LabelConstant::LBL_PITS,
            $controller->getRow([
                LabelConstant::LBL_PIT_STOP,
                LabelConstant::LBL_LONG_PIT_STOP,
                LabelConstant::LBL_SUCCESS_SHORT_PIT_STOP,
                LabelConstant::LBL_FAIL_SHORT_PIT_STOP],
                false
            ),
            $controller->getRow([
                $pitStopEventCollection->length(),
                $pitStopEventCollection->filterPitStop(true)->length(),
                $pitStopEventCollection->filterPitStop(false)->length(),
                $pitStopEventCollection->filterPitStop(false, true)->length(),
            ])
        ];
        return $controller->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }

    public static function displayPlayerPitStop(Player $objPlayer): string
    {
        $pitStopEventCollection = $objPlayer->getEventCollection()->filterBy(PitStopEvent::class);

        return '<br>Stands : <span class="badge bg-info" title="Nombre d\'arrêts">'.$pitStopEventCollection->length()
            .'</span> - <span class="badge bg-success" title="Rapides réussis">'.$pitStopEventCollection->filterPitStop(false)->length()
            .'</span> - <span class="badge bg-danger" title ="Rapides échoués">'.$pitStopEventCollection->filterPitStop(false, true)->length()
            .'</span>';
    }
}
