<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\EngineTest;
use src\Entity\Game;

class EngineController extends GameController
{

    public static function displayEngine(Game $objGame): string
    {
        $controller = new EngineController($objGame);

        $engineTestCollection = $objGame->getTestCollection()->filterBy(EngineTest::class);

        $content = '';
        for ($i=1; $i<=10; $i++) {
            $styles = [$i<=4 ? ' class="bg-dark text-white"' : '', $i<=4 ? ' class="bg-dark text-white"' : '', '',''];
            $content .= $controller->getRow([
                $i,
                $engineTestCollection->countScores($i),
                $i+10,
                $engineTestCollection->countScores($i+10)],
                true,
                $styles
            );
        }

        $attributes = [
            LabelConstant::LBL_ENGINE,
            $controller->getRow([
                LabelConstant::LBL_THROWN_DICE,
                LabelConstant::LBL_FAILED_DICE,
                LabelConstant::LBL_FORCED_DICE],
                false
            ),
            $controller->getRow([
                $engineTestCollection->length(),
                $engineTestCollection->countFailItems(),
                $engineTestCollection->countForcedItems()],
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
