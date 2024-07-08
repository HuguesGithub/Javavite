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

        $bodyTestCollection = $objGame->getTestCollection()->filterBy(BodyTest::class);

        $content = '';
        for ($i=1; $i<=10; $i++) {
            $styles = [$i<=1 ? ' class="bg-dark text-white"' : '', $i<=1 ? ' class="bg-dark text-white"' : '', '',''];
            $content .= $controller->getRow([
                $i,
                $bodyTestCollection->countScores($i),
                $i+10,
                $bodyTestCollection->countScores($i+10)],
                true,
                $styles
            );
        }

        $attributes = [
            LabelConstant::LBL_BODY,
            $controller->getRow([
                LabelConstant::LBL_THROWN_DICE,
                LabelConstant::LBL_FAILED_DICE,
                LabelConstant::LBL_FORCED_DICE],
                false
            ),
            $controller->getRow([
                $bodyTestCollection->length(),
                $bodyTestCollection->countFailItems(),
                $bodyTestCollection->countForcedItems()],
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
