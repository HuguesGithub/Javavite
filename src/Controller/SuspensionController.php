<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Game;
use src\Entity\SuspensionTest;


class SuspensionController extends GameController
{

    public static function displaySuspension(Game $objGame): string
    {
        $controller = new SuspensionController($objGame);

        $suspensionEventCollection = $objGame->getEventCollection()->getClassEvent(SuspensionTest::class);

        $content = '';
        for ($i=1; $i<=10; $i++) {
            $styles = [$i<=4 ? ' class="bg-dark text-white"' : '', $i<=4 ? ' class="bg-dark text-white"' : '', '',''];
            $content .= $controller->getRow([
                $i,
                $suspensionEventCollection->filter([ConstantConstant::CST_SCORE=>$i])->length(),
                $i+10,
                $suspensionEventCollection->filter([ConstantConstant::CST_SCORE=>$i+10])->length()],
                true,
                $styles
            );
        }

        $attributes = [
            LabelConstant::LBL_SUSPENSION,
            $controller->getRow([
                LabelConstant::LBL_THROWN_DICE,
                LabelConstant::LBL_FAILED_DICE],
                false
            ),
            $controller->getRow([
                $suspensionEventCollection->length(),
                $suspensionEventCollection->filter([ConstantConstant::CST_FAIL=>true])->length()],
                false),
            $controller->getRow([
                LabelConstant::LBL_THROW,
                LabelConstant::LBL_QUANTITY,
                LabelConstant::LBL_THROW,
                LabelConstant::LBL_QUANTITY],
                false),
            $content
        ];
        return $controller->getRender(TemplateConstant::TPL_CARD_DOUBLE_TABLE, $attributes);
    }

}
