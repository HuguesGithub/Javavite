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

        $engineEventCollection = $objGame->getEventCollection()->getClassEvent(EngineTest::class);

        $content = '';
        for ($i=1; $i<=10; $i++) {
            $styles = [$i<=4 ? ' class="bg-dark text-white"' : '', $i<=4 ? ' class="bg-dark text-white"' : '', '',''];
            $content .= $controller->getRow([
                $i,
                $engineEventCollection->filter(['score'=>$i])->length(),
                $i+10,
                $engineEventCollection->filter(['score'=>$i+10])->length()],
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
                $engineEventCollection->length(),
                $engineEventCollection->filter([ConstantConstant::CST_FAIL=>true])->length(),
                $engineEventCollection->filter([ConstantConstant::CST_INFLICTED=>true])->length()],
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
