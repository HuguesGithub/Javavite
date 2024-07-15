<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\FuelEvent;
use src\Entity\Game;
use src\Entity\Player;

class FuelController extends GameController
{

    public static function displayFuel(Game $objGame): string
    {
        $controller = new FuelController($objGame);

        $fuelEventCollection = $objGame->getEventCollection()->getClassEvent(FuelEvent::class);

        $attributes = [
            LabelConstant::LBL_FUELS,
            // class additionnelle pour card-body
            'p-0',
            $controller->getRow([
                LabelConstant::LBL_QUANTITY,
                LabelConstant::LBL_1GEAR,
                LabelConstant::LBL_2GEAR,
                LabelConstant::LBL_3GEAR],
                false
            ),
            $controller->getRow([
                $fuelEventCollection->length(),
                $fuelEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_1GEAR])->length(),
                $fuelEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_2GEAR])->length(),
                $fuelEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_3GEAR])->length(),
            ])
        ];
        return $controller->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }

    public static function displayPlayerFuel(Player $objPlayer): string
    {
        $fuelEventCollection = $objPlayer->getEventCollection()->getClassEvent(FuelEvent::class);

        return '<br>Consommation : <span class="badge bg-info" title="Total">'.$fuelEventCollection->length()
            .'</span> - <span class="badge bg-success" title="1 rapport">'
            .$fuelEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_1GEAR])->length()
            .'</span> - <span class="badge bg-warning" title ="2 rapports">'
            .$fuelEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_2GEAR])->length()
            .'</span> - <span class="badge bg-danger" title ="3 rapports">'
            .$fuelEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_3GEAR])->length()
            .'</span>';
    }

}
