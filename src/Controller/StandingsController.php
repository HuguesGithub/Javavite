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
        // TODO : Trier les Players par ordre d'arrivée, une fois que endPosition sera renseigné.
        $playerCollection = $objGame->getPlayerCollection();
        $playerCollection->rewind();
        while ($playerCollection->valid()) {
            $objPlayer = $playerCollection->current();
            $content .= $objPlayer->getController()->getRowStanding();
            $playerCollection->next();
        }
        /*
        foreach ($objGame->getPlayers() as $objPlayer) {
            $objPlayers[$objPlayer->getEndPosition()] = $objPlayer;
        }
        for ($i=1; $i<=10; $i++) {
            if (!isset($objPlayers[$i])) {
                continue;
            }
            $content .= $controller->getRow([
                $objPlayers[$i]->getPlayerName(),
                $i,
                $objPlayers[$i]->getStartPosition(),
                $objPlayers[$i]->getMoves(),
                $objPlayers[$i]->getDnf()
            ]);
        }
            */

        $attributes = [
            LabelConstant::LBL_STANDINGS,
            $controller->getRow([
                LabelConstant::LBL_PILOTE,
                LabelConstant::LBL_FINISH_POSITION,
                LabelConstant::LBL_START_POSITION,
                LabelConstant::LBL_MOVES,
                LabelConstant::LBL_DNF],
                false
            ),
            $content
        ];
        return $controller->getRender(TemplateConstant::TPL_CARD_SIMPLE_TABLE, $attributes);
    }
    
}
