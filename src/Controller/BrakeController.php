<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\BrakeEvent;
use src\Entity\Game;
use src\Entity\Player;

class BrakeController extends GameController
{

    public static function displayBrakes(Game $objGame): string
    {
        $controller = new BrakeController($objGame);

        $brakeEventCollection = $objGame->getEventCollection()->getClassEvent(BrakeEvent::class);

        $attributes = [
            LabelConstant::LBL_BRAKES,
            // class additionnelle pour card-body
            'p-0',
            $controller->getRow([
                LabelConstant::LBL_QUANTITY,
                LabelConstant::LBL_BRAKE,
                LabelConstant::LBL_TRAIL,
                LabelConstant::LBL_FUEL,
                LabelConstant::LBL_BLOCKED],
                false
            ),
            $controller->getRow([
                $brakeEventCollection->length(),
                $brakeEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_BRAKE])->length(),
                $brakeEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_TRAIL])->length(),
                $brakeEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_FUEL])->length(),
                $brakeEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_BLOCKED])->length(),
            ])
        ];
        return $controller->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }

    public static function displayPlayerBrake(Player $objPlayer): string
    {
        $brakeEventCollection = $objPlayer->getEventCollection()->getClassEvent(BrakeEvent::class);

        return '<br>Freins : <span class="badge bg-info" title="Total">'.$brakeEventCollection->length()
            .' Consommés</span> - <span class="badge bg-warning" title="Freins">'
            .$brakeEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_BRAKE])->length()
            .'</span> - <span class="badge bg-warning" title ="Aspiration">'
            .$brakeEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_TRAIL])->length()
            .'</span> - <span class="badge bg-warning" title ="Consommation">'
            .$brakeEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_FUEL])->length()
            .'</span> - <span class="badge bg-danger" title ="Blocage">'
            .$brakeEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_BLOCKED])->length()
            .'</span>';
    }

}
