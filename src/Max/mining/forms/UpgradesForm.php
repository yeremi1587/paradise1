<?php

namespace Max\mining\forms;

use Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;
use Max\mining\Main;

class UpgradesForm extends SimpleForm {
    public function __construct(Player $player) {
        parent::__construct(function (Player $player, ?int $data = null) {
            if($data === null) {
                $form = new MainForm();
                $form->sendTo($player);
                return;
            }

            if($data >= 0 && $data <= 2) {
                $this->purchaseUpgrade($player, $data + 1);
            }
        });

        $this->setTitle("§l§6Mining Upgrades");
        $this->setContent("§7Purchase upgrades to increase your mining rewards:");
        
        $this->addButton("§a» §fLevel 1 Upgrade\n§72x Coins - $10,000", 0);
        $this->addButton("§a» §fLevel 2 Upgrade\n§73x Coins - $25,000", 1);
        $this->addButton("§a» §fLevel 3 Upgrade\n§74x Coins - $50,000", 2);
        $this->addButton("§c« §fBack to Menu", 3);
    }

    private function purchaseUpgrade(Player $player, int $level): void {
        $costs = [
            1 => 10000,
            2 => 25000,
            3 => 50000
        ];

        if(isset($costs[$level])) {
            $cost = $costs[$level];
            $economy = Main::getInstance()->getEconomy();
            
            if($economy->myMoney($player) >= $cost) {
                $economy->reduceMoney($player, $cost);
                $player->sendMessage("§aSuccessfully purchased Level $level Upgrade!");
            } else {
                $player->sendMessage("§cYou don't have enough money for this upgrade!");
            }
        }
    }
}