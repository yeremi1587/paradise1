<?php

declare(strict_types=1);

namespace Max\koth\Integration;

use pocketmine\player\Player;
use Max\koth\KOTH;
use rxduz\factions\faction\FactionManager;
use pocketmine\utils\TextFormat as TF;
use rxduz\factions\player\PlayerManager;
use rxduz\factions\faction\Faction;

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

        // First check if player is in any faction
        $playerSession = PlayerManager::getInstance()->getSessionByName($player->getName());
        if ($playerSession === null) {
            $player->sendMessage(TF::RED . "You cannot participate in KOTH without being in a faction.");
            return false;
        }

        $faction = $playerSession->getFaction();
        if (!$faction instanceof Faction) {
            $player->sendMessage(TF::RED . "You cannot participate in KOTH without being in a faction.");
            return false;
        }

        if ($faction->getPower() < $this->minPower) {
            $player->sendMessage(TF::RED . "Your faction needs at least " . $this->minPower . " power to participate in KOTH.");
            return false;
        }

        return true;
    }

    public function getMinPower(): int {
        return $this->minPower;
    }
}