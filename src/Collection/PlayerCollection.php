<?php
namespace src\Collection;

use src\Entity\Player;

class PlayerCollection extends Collection
{
    public function getPlayerByName(string $playerName): ?Player
    {
        $this->rewind();
        while ($this->valid()) {
            $objPlayer = $this->current();
            if ($objPlayer->getPlayerName()==$playerName) {
                return $objPlayer;
            }
            $this->next();
        }
        $nb = $this->length();
        $objPlayer = new Player($playerName, $nb+1);
        $this->addItem($objPlayer, $nb);
        return $objPlayer;
    }
}
