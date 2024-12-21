<?php

namespace Paradise\Mining\forms;

use Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;

class MainForm extends SimpleForm {
    public function __construct() {
        parent::__construct(function (Player $player, ?int $data = null) {
            if($data === null) return;
            
            switch($data) {
                case 0:
                    $form = new StatsForm($player);
                    $player->sendForm($form);
                    break;
                case 1:
                    $form = new UpgradesForm($player);
                    $player->sendForm($form);
                    break;
                case 2:
                    $form = new ExchangeForm($player);
                    $player->sendForm($form);
                    break;
                case 3:
                    $player->sendMessage("§cTop miners feature coming soon!");
                    break;
            }
        });

        $this->setTitle("§l§6Mining System");
        $this->setContent("§7Select an option:");
        
        $this->addButton("§a» §fMining Stats\n§7View your statistics", 0, "textures/ui/icon_book_writable");
        $this->addButton("§a» §fUpgrades\n§7Buy mining upgrades", 0, "textures/items/diamond_pickaxe");
        $this->addButton("§a» §fExchange Blocks\n§7Trade blocks for coins", 0, "textures/items/emerald");
        $this->addButton("§a» §fTop Miners\n§7View leaderboard", 0, "textures/ui/icon_multiplayer");
    }
}