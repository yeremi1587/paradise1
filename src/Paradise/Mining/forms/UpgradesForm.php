<?php

namespace Paradise\Mining\forms;

use Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;
use Paradise\Mining\Main;

class UpgradesForm extends SimpleForm {
    public function __construct(Player $player) {
        parent::__construct(function (Player $player, ?int $data = null) {
            if($data === null) {
                $form = new MainForm();
                $form->send($player);
                return;
            }

            if($data >= 0 && $data <= 2) {
                $this->purchaseUpgrade($player, $data + 1);
            }
        });

        $this->setTitle("§l§6Mining Upgrades");
        $this->setContent("§7Purchase upgrades to increase your mining rewards:");
        
        $this->addButton("§a» §fLevel 1 Upgrade\n§72x Coins - $10,000", 0, "textures/ui/icon_upgrade");
        $this->addButton("§a» §fLevel 2 Upgrade\n§73x Coins - $25,000", 1, "textures/ui/icon_upgrade");
        $this->addButton("§a» §fLevel 3 Upgrade\n§74x Coins - $50,000", 2, "textures/ui/icon_upgrade");
        $this->addButton("§c« §fBack to Menu", 3, "textures/ui/arrow_left");
    }

    private function purchaseUpgrade(Player $player, int $level): void {
        $costs = [
            1 => [10000, 2.0],
            2 => [25000, 3.0],
            3 => [50000, 4.0]
        ];

        if(isset($costs[$level])) {
            [$cost, $multiplier] = $costs[$level];
            $economy = Main::getInstance()->getEconomy();
            $statsManager = Main::getInstance()->getStatsManager();
            
            if($economy->myMoney($player) >= $cost) {
                $economy->reduceMoney($player, $cost);
                $statsManager->setMultiplier($player, $multiplier);
                $player->sendMessage("§aSuccessfully purchased Level $level Upgrade!");
            } else {
                $player->sendMessage("§cYou don't have enough money for this upgrade!");
            }
        }
    }
}
