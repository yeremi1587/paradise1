<?php

namespace Max\mining\forms;

use pocketmine\player\Player;
use Vecnavium\FormsUI\SimpleForm;

class MainForm {
    public function sendTo(Player $player): void {
        $form = new SimpleForm(function(Player $player, ?int $data) {
            if($data === null) return;
            
            switch($data) {
                case 0: // Stats
                    // TODO: Implement stats form
                    break;
                case 1: // Upgrades
                    // TODO: Implement upgrades form
                    break;
                case 2: // Block Exchange
                    // TODO: Implement block exchange form
                    break;
                case 3: // Leaderboard
                    // TODO: Implement leaderboard form
                    break;
            }
        });

        $form->setTitle("§l§6Mining System");
        $form->setContent("§7Select an option:");
        $form->addButton("§l§bMining Stats\n§r§7View your statistics", 0, "textures/ui/icon_book_writable");
        $form->addButton("§l§aMining Upgrades\n§r§7Purchase upgrades", 0, "textures/ui/icon_upgrade");
        $form->addButton("§l§eBlock Exchange\n§r§7Exchange blocks for coins", 0, "textures/ui/icon_trade");
        $form->addButton("§l§6Leaderboard\n§r§7View top miners", 0, "textures/ui/icon_multiplayer");
        
        $form->sendToPlayer($player);
    }
}