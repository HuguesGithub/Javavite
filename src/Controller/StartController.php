<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Game;
use src\Entity\Player;
use src\Entity\StartTest;

class StartController extends GameController
{
    public static function displayStart(Game $objGame): string
    {
        $controller = new StartController($objGame);

        $startEventCollection = $objGame->getEventCollection()->getClassEvent(StartTest::class);

        $attributes = [
            LabelConstant::LBL_START,
            // class additionnelle pour card-body
            'p-0',
            $controller->getRow([
                LabelConstant::LBL_THROWN_DICE,
                LabelConstant::LBL_FAIL_START,
                LabelConstant::LBL_SUCCESS_START],
                false
            ),
            $controller->getRow([
                $startEventCollection->length(),
                $startEventCollection->filter([ConstantConstant::CST_FAIL=>true])->length(),
                $startEventCollection->filter([ConstantConstant::CST_SUCCESS=>true])->length()])
        ];

        return $controller->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }

    public static function displayPlayerStart(Player $objPlayer): string
    {
        $startEventCollection = $objPlayer->getEventCollection()->getClassEvent(StartTest::class);
        
        $quantity = $startEventCollection->length();
        $fail = $startEventCollection->filter([ConstantConstant::CST_FAIL=>true])->length();
        $success = $startEventCollection->filter([ConstantConstant::CST_SUCCESS=>true])->length();

        return 'Départ : <span class="badge bg-info">'.$quantity.sprintf(' Jet%1$s effectué%1$s', $quantity>1 ? 's' : '')
            .'</span> - <span class="badge bg-danger">'.$fail.' Calage'.($fail>1 ? 's' : '')
            .'</span> - <span class="badge bg-success">'.$success.sprintf(' Super%1$s départ%1$s', $success>1 ? 's' : '')
            .'</span>';
    }

}
