<?php

namespace Max\mining\forms;

use Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;
use Max\mining\Main;

class ExchangeForm extends SimpleForm {
    public function __construct(Player $player) {
        parent::__construct(function (Player $player, ?int $data = null) {
            if($data === null) {
                $form = new MainForm();
                $form->sendTo($player);
                return;
            }

            // Handle exchange logic here
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
}