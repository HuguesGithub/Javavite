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

        $testCollection = $objGame->getTestCollection();
        $testCollection->rewind();
        $startTestCollection = $testCollection->filterBy(StartTest::class);
        $startTestCollection->rewind();
        $quantity = 0;
        $fail = 0;
        $success = 0;

        while ($startTestCollection->valid()) {
            $objStartTest = $startTestCollection->current();
            $quantity++;
            if ($objStartTest->getScore()==1) {
                $fail++;
            } elseif ($objStartTest->getScore()==20) {
                $success++;
            } else {
                // Ne rien faire
            }
            $startTestCollection->next();
        }

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
            $controller->getRow([$quantity, $fail, $success])
        ];

        return $controller->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }

    public static function displayPlayerStart(Player $objPlayer): string
    {
        $testCollection = $objPlayer->getTestCollection();
        $testCollection->rewind();
        $startTestCollection = $testCollection->filterBy(StartTest::class);
        $startTestCollection->rewind();
        $quantity = 0;
        $fail = 0;
        $success = 0;

        while ($startTestCollection->valid()) {
            $objStartTest = $startTestCollection->current();
            $quantity++;
            if ($objStartTest->getScore()==1) {
                $fail++;
            } elseif ($objStartTest->getScore()==20) {
                $success++;
            } else {
                // Ne rien faire
            }
            $startTestCollection->next();
        }

        return 'Départ : <span class="badge bg-info">'.$quantity.sprintf(' Jet%1$s effectué%1$s', $quantity>1 ? 's' : '')
            .'</span> - <span class="badge bg-danger">'.$fail.' Calage'.($fail>1 ? 's' : '')
            .'</span> - <span class="badge bg-success">'.$success.sprintf(' Super%1$s départ%1$s', $success>1 ? 's' : '')
            .'</span>';
    }

}
