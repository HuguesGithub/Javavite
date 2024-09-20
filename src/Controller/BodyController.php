<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\BodyTest;
use src\Entity\Game;

class BodyController extends GameController
{

    public static function displayBody(Game $objGame): string
    {
        $controller = new BodyController($objGame);

        $bodyEventCollection = $objGame->getEventCollection()->getClassEvent(BodyTest::class);

        $content = '';
        for ($i=1; $i<=10; $i++) {
            $content .= $controller->getRow([
                $i,
                $bodyEventCollection->filter([ConstantConstant::CST_SCORE=>$i])->length(),
                $i+10,
                $bodyEventCollection->filter([ConstantConstant::CST_SCORE=>$i+10])->length()],
                true,
                [ConstantConstant::CST_CLASS_BG_LIGHT, $i<=1 ? ' class="bg-danger"' : '', ConstantConstant::CST_CLASS_BG_LIGHT, '']
            );
        }

        $style = ' class="bg-dark text-white"';
        $attributes = [
            LabelConstant::LBL_BODY,
            $controller->getRow([
                LabelConstant::LBL_THROWN_DICE,
                LabelConstant::LBL_FAILED_DICE,
                LabelConstant::LBL_FORCED_DICE],
                false,
                array_fill(0, 3, $style)
            ),
            $controller->getRow([
                $bodyEventCollection->length(),
                $bodyEventCollection->filter([ConstantConstant::CST_FAIL=>true])->length(),
                $bodyEventCollection->filter([ConstantConstant::CST_INFLICTED=>true])->length()],
                false),
            $controller->getRow([
                LabelConstant::LBL_THROW,
                LabelConstant::LBL_QUANTITY,
                LabelConstant::LBL_THROW,
                LabelConstant::LBL_QUANTITY],
                false,
                array_fill(0, 4, $style)),
            $content
        ];
        return $controller->getRender(TemplateConstant::TPL_CARD_DOUBLE_TABLE, $attributes);
    }

}
