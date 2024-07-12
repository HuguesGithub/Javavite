<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Game;
use src\Entity\Player;
use src\Entity\TaqEvent;

class TaqController extends GameController
{

    public static function displayTaQ(Game $objGame): string
    {
        $controller = new TaqController($objGame);

        $taqEventCollection = $objGame->getEventCollection()->filterBy(TaqEvent::class);

        $attributes = [
            LabelConstant::LBL_TAQ,
            $controller->getRow([LabelConstant::LBL_QUANTITY], false),
            $controller->getRow([$taqEventCollection->length()])
        ];
        return $controller->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }

    public static function displayPlayerTaQ(Player $objPlayer): string
    {
        $taqEventCollection = $objPlayer->getEventCollection()->filterBy(TaqEvent::class);
        $nb = $taqEventCollection->length();

        return '<br>Tête à queue : <span class="badge bg-'.($nb==0?'success':'danger').'">'.$nb.'</span>';
    }
}
