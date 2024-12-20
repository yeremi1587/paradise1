<?php

namespace Max\mining\forms;

use Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;

class MainForm extends SimpleForm {
    public function __construct() {
        parent::__construct(function (Player $player, ?int $data = null) {
            if($data === null) return;
            
            switch($data) {
                case 0: // Stats
                    // TODO: Show stats form
                    break;
                case 1: // Upgrades
                    // TODO: Show upgrades form
                    break;
                case 2: // Exchange
                    // TODO: Show exchange form
                    break;
                case 3: // Top miners
                    // TODO: Show top miners form
                    break;
            }
        });

        $this->setTitle("§l§6Mining System");
        $this->setContent("§7Select an option:");
        
        $this->addButton("§a» §fMining Stats\n§7View your statistics", 0);
        $this->addButton("§a» §fUpgrades\n§7Buy mining upgrades", 1);
        $this->addButton("§a» §fExchange Blocks\n§7Trade blocks for coins", 2);
        $this->addButton("§a» §fTop Miners\n§7View leaderboard", 3);
    }
}
?>