<?php
namespace src\Collection;

use src\Entity\Player;

class PlayerCollection extends Collection
{
    public function getPlayerByName(string $playerName): ?Player
    {
        $key = -1;
        $this->rewind();
        while ($this->valid()) {
            $objPlayer = $this->current();
            if ($objPlayer->getPlayerName()==$playerName) {
                return $objPlayer;
            } elseif ($objPlayer->getPlayerName()=='unknown') {
                $key = $this->key();
            } else {
                // Rien de particulier
            }
            $this->next();
        }
        if ($key!=-1) {
            $this->deleteItem($key);
            $objPlayer = new Player($playerName, $key+1);
            $this->addItem($objPlayer, $key);
            return $objPlayer;
        }
        return null;
    }
}
