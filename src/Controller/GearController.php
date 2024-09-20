<?php
namespace src\Controller;

use src\Collection\EventCollection;
use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Game;
use src\Entity\GearEvent;

class GearController extends EventController
{

    public static function displayGears(EventCollection $gearEventCollection): string
    {
        $controller = new UtilitiesController();
        $content = '';

        $arr = [
            1 => ['min'=>1, 'max'=>2, 'exp'=>'1.50'],
            2 => ['min'=>2, 'max'=>4, 'exp'=>'3.33'],
            3 => ['min'=>4, 'max'=>8, 'exp'=>'6.37'],
            4 => ['min'=>7, 'max'=>12, 'exp'=>'9.50'],
            5 => ['min'=>11, 'max'=>20, 'exp'=>'15.50'],
            6 => ['min'=>21, 'max'=>30, 'exp'=>'25.50']
        ];
                    
        foreach ($arr as $key => $minMax) {
            $min = $minMax['min'];
            $max = $minMax['max'];
            $exp = $minMax['exp'];

            $content .= $controller->getRow(
                array_merge([ConstantConstant::CST_NBSP, 'Moyenne', 'Attendue'], range($min, $max), ($max!=$min+9)?[ConstantConstant::CST_NBSP]:[]),
                false,
                array_merge(array_fill(0, 4+$max-$min, ' class="bg-dark text-white"'), [' colspan="'.(9-$max+$min).'" class="bg-light"'])
            );

            $line = [
                $key,
                round($gearEventCollection->filter([ConstantConstant::CST_TYPE=>$key])->sum()/max(1, $gearEventCollection->filter([ConstantConstant::CST_TYPE=>$key])->length()), 2),
                $exp
            ];
            for ($i=$min; $i<=$max; $i++) {
                $line[] = $gearEventCollection->filter([ConstantConstant::CST_TYPE=>$key, ConstantConstant::CST_SCORE=>$i])->length();
            }
            if ($max!=$min+9) {
                $line [] = ConstantConstant::CST_NBSP;
            }

            $content .= $controller->getRow(
                $line,
                true,
                array_merge([ConstantConstant::CST_CLASS_BG_LIGHT], array_fill(0, 3+$max-$min, ' class="bg-g'.$key.'"'), [' colspan="'.(9-$max+$min).'"'.ConstantConstant::CST_CLASS_BG_LIGHT])
            );
        }

        return $content;
    }

}
