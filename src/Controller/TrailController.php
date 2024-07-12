<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Game;
use src\Entity\Player;
use src\Entity\TrailEvent;

class TrailController extends GameController
{

    public static function displayTrails(Game $objGame): string
    {
        $controller = new TrailController($objGame);

        $trailEventCollection = $objGame->getEventCollection()->filterBy(TrailEvent::class);

        $attributes = [
            LabelConstant::LBL_TRAILS,
            // class additionnelle pour card-body
            'p-0',
            $controller->getRow([
                LabelConstant::LBL_QUANTITY,
                LabelConstant::LBL_TAKEN_TRAIL,
                LabelConstant::LBL_DECLINED_TRAIL],
                false
            ),
            $controller->getRow([
                $trailEventCollection->length(),
                $trailEventCollection->filterBy(ConstantConstant::CST_ACCEPTED)->length(),
                $trailEventCollection->filterBy(ConstantConstant::CST_DECLINED)->length(),
            ])
        ];
        return $controller->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }

    public static function displayPlayerTrail(Player $objPlayer): string
    {
        $trailEventCollection = $objPlayer->getEventCollection()->filterBy(TrailEvent::class);

        return '<br>Aspirations : <span class="badge bg-info" title="Total">'.$trailEventCollection->length()
            .' Proposées</span> - <span class="badge bg-success" title="Acceptées">'.$trailEventCollection->filterBy(ConstantConstant::CST_ACCEPTED)->length()
            .'</span> - <span class="badge bg-danger" title ="Déclinées">'.$trailEventCollection->filterBy(ConstantConstant::CST_DECLINED)->length()
            .'</span>';
    }

}
