<?php

namespace Max\mining\forms;

use Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;
use Max\mining\Main;

class StatsForm extends SimpleForm {
    public function __construct(Player $player) {
        parent::__construct(function (Player $player, ?int $data = null) {
            if($data === null) {
                $form = new MainForm();
                $form->sendTo($player);
                return;
            }
        });

        $this->setTitle("§l§6Mining Statistics");
        $this->setContent(
            "§7Your Mining Statistics:\n\n" .
            "§fBlocks Mined: §a0\n" .
            "§fCoins Earned: §a0\n" .
            "§fCurrent Multiplier: §ax1\n" .
            "§fSpecial Blocks Found: §a0"
        );
        
        $this->addButton("§c« §fBack to Menu", 0);
    }
}