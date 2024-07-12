<?php
namespace src\Controller;

use src\Collection\GearCollection;
use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Game;

class GearController extends GameController
{

    public static function displayGears(Game $objGame): string
    {
        $controller = new GearController($objGame);

        $arr = [
            1 => ['min'=>1, 'max'=>2],
            2 => ['min'=>2, 'max'=>4],
            3 => ['min'=>4, 'max'=>8],
            4 => ['min'=>7, 'max'=>12],
            5 => ['min'=>11, 'max'=>20],
            6 => ['min'=>21, 'max'=>30]
        ];
        $content = '';

        foreach ($arr as $key => $minMax) {
            $min = $minMax['min'];
            $max = $minMax['max'];

            $arrContent = [];
            $styles = [];
            if ($min!=1) {
                $arrContent[] = ConstantConstant::CST_NBSP;
                $styles[] = ' colspan="'.($min-1).'"';
            }
            for ($i=$min; $i<=$max; $i++) {
                $arrContent[] = $objGame->getGearCollection()->filterBy(['gear'=>$key, 'score'=>$i])->length();
                $styles[] = ' class="bg-g'.$key.'"';
            }
            if ($max!=30) {
                $arrContent[] = ConstantConstant::CST_NBSP;
                $styles[] = ' colspan="'.(30-$max).'"';
            }
            $content .= $controller->getRow($arrContent, true, $styles);
        }

        $attributes = [
            LabelConstant::LBL_MOVE_DICE,
            // class additionnelle pour card-body
            'px-0',
            $controller->getRow(range(1, 30), false),
            $content
        ];
        return $controller->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }

}
