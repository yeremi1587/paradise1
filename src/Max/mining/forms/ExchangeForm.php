<?php

namespace Max\mining\forms;

use Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;
use Max\mining\Main;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;

class ExchangeForm extends SimpleForm {
    public function __construct(Player $player) {
        parent::__construct(function (Player $player, ?int $data = null) {
            if($data === null) {
                $form = new MainForm();
                $form->sendTo($player);
                return;
            }

            if($data < 4) {
                $this->handleExchange($player, $data);
            }
        });

        $this->setTitle("§l§6Block Exchange");
        $this->setContent(
            "§7Exchange your mined blocks for coins:\n\n" .
            "§fGold Ore: §a10 coins\n" .
            "§fDiamond Ore: §a50 coins\n" .
            "§fAncient Debris: §a100 coins\n" .
            "§fEpic Ore: §a500 coins"
        );
        
        $this->addButton("§a» §fExchange Gold Ore", 0);
        $this->addButton("§a» §fExchange Diamond Ore", 1);
        $this->addButton("§a» §fExchange Ancient Debris", 2);
        $this->addButton("§a» §fExchange Epic Ore", 3);
        $this->addButton("§c« §fBack to Menu", 4);
    }

    private function handleExchange(Player $player, int $type): void {
        $items = [
            0 => [VanillaBlocks::GOLD_ORE(), 10],
            1 => [VanillaBlocks::DIAMOND_ORE(), 50],
            2 => [VanillaBlocks::ANCIENT_DEBRIS(), 100],
            3 => [VanillaBlocks::GOLD_BLOCK(), 500] // Using gold block as epic ore for example
        ];

        if(!isset($items[$type])) {
            return;
        }

        [$block, $reward] = $items[$type];
        $inventory = $player->getInventory();
        $count = 0;

        foreach($inventory->getContents() as $slot => $item) {
            if($item->getTypeId() === $block->asItem()->getTypeId()) {
                $count += $item->getCount();
                $inventory->clear($slot);
            }
        }

        if($count > 0) {
            $totalReward = $count * $reward;
            Main::getInstance()->getEconomy()->addMoney($player, $totalReward);
            Main::getInstance()->getStatsManager()->addCoinsEarned($player, $totalReward);
            $player->sendMessage("§aExchanged $count blocks for $totalReward coins!");
        } else {
            $player->sendMessage("§cYou don't have any blocks to exchange!");
        }
    }
}