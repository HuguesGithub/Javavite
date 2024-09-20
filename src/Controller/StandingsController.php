<?php
namespace src\Controller;

use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Game;

class StandingsController extends GameController
{

    public static function displayStandings(Game &$objGame): string
    {
        $controller = new StandingsController($objGame);

        $content = '';
        $playerCollection = $objGame->getPlayerCollection();
        $playerCollection->rewind();
        while ($playerCollection->valid()) {
            $objPlayer = $playerCollection->current();
            $content .= $objPlayer->getController()->getRowStanding();
            $playerCollection->next();
        }

        $style = ' class="bg-dark text-white"';
        $attributes = [
            LabelConstant::LBL_STANDINGS,
            // class additionnelle pour card-body
            'p-0',
            $controller->getRow([
                LabelConstant::LBL_PILOTE,
                LabelConstant::LBL_FINISH_POSITION,
                LabelConstant::LBL_START_POSITION,
                LabelConstant::LBL_MOVES,
                LabelConstant::LBL_DNF],
                false,
                array_fill(0, 5, $style)
            ),
            $content
        ];
        return $controller->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }
    
}
