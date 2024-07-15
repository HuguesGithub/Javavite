<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\DnfEvent;
use src\Entity\Game;

class DnfController extends GameController
{
    public static function displayDnfs(Game $objGame): string
    {
        $controller = new DnfController($objGame);

        $dnfEventCollection = $objGame->getEventCollection()->getClassEvent(DnfEvent::class);

        $attributes = [
            LabelConstant::LBL_DNFS,
            // class additionnelle pour card-body
            'p-0',
            $controller->getRow([
                LabelConstant::LBL_QUANTITY,
                LabelConstant::LBL_BODY,
                LabelConstant::LBL_SUSP,
                LabelConstant::LBL_ENGINE,
                LabelConstant::LBL_BLOCKED,
                LabelConstant::LBL_TIRE],
                false
            ),
            $controller->getRow([
                $dnfEventCollection->length(),
                $dnfEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_BODY])->length(),
                $dnfEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_SUSPENSION])->length(),
                $dnfEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_ENGINE])->length(),
                $dnfEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_BLOCKED])->length(),
                $dnfEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_TIRE])->length(),
            ])
        ];
        return $controller->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }

}
