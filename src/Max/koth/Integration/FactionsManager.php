<?php

declare(strict_types=1);

namespace Max\koth\Integration;

use pocketmine\player\Player;
use Max\koth\KOTH;
use rxduz\factions\faction\FactionManager;
use pocketmine\utils\TextFormat as TF;

class FactionsManager {
    private KOTH $plugin;
    private int $minPower;

    public function __construct(KOTH $plugin, int $minPower = 100) {
        $this->plugin = $plugin;
        $this->minPower = $minPower;
    }

    public function canParticipate(Player $player): bool {
        if (!$this->plugin->getServer()->getPluginManager()->getPlugin("AdvancedFactions")) {
            return true;
        }

        $faction = FactionManager::getInstance()->getFactionByName($player->getName());
        if ($faction === null) {
            $player->sendMessage(TF::RED . "No puedes participar en el KOTH sin pertenecer a una facción.");
            return false;
        }

        if ($faction->getPower() < $this->minPower) {
            $player->sendMessage(TF::RED . "Tu facción necesita al menos " . $this->minPower . " de poder para participar en el KOTH.");
            return false;
        }

        return true;
    }

    public function getMinPower(): int {
        return $this->minPower;
    }
}