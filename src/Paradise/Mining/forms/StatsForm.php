<?php

namespace Paradise\Mining\forms;

use Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;
use Paradise\Mining\Main;

class StatsForm extends SimpleForm {
    public function __construct(Player $player) {
        parent::__construct(function (Player $player, ?int $data = null) {
            if($data !== null) {
                $form = new MainForm();
                $form->send($player);
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
        
        $this->addButton("§c« §fBack to Menu", 0, "textures/ui/arrow_left");
    }
}