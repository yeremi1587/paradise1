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

        $stats = Main::getInstance()->getStatsManager()->getStats($player);
        
        $this->setTitle("§l§6Mining Statistics");
        $this->setContent(
            "§7Your Mining Statistics:\n\n" .
            "§fBlocks Mined: §a" . $stats['blocksMined'] . "\n" .
            "§fCoins Earned: §a" . $stats['coinsEarned'] . "\n" .
            "§fCurrent Multiplier: §ax" . $stats['multiplier'] . "\n" .
            "§fSpecial Blocks Found: §a" . $stats['specialBlocksFound']
        );
        
        $this->addButton("§c« §fBack to Menu", 0);
    }
}