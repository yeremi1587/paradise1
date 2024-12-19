<?php

declare(strict_types=1);

namespace Max\koth\Integration;

use pocketmine\player\Player;
use Max\koth\KOTH;

class FactionsManager {
    private KOTH $plugin;
    private int $minPower;

    public function __construct(KOTH $plugin, int $minPower = 100) {
        $this->plugin = $plugin;
        $this->minPower = $minPower;
    }

    public function canParticipate(Player $player): bool {
        if (!$this->plugin->getServer()->getPluginManager()->getPlugin("AdvancedFactions")) {
            return true; // Si AdvancedFactions no está instalado, permitir participación
        }

        $faction = \AdvancedFactions\Manager::getInstance()->getPlayerFaction($player);
        if ($faction === null) {
            return false; // El jugador no está en ninguna facción
        }

        return $faction->getPower() >= $this->minPower;
    }

    public function getMinPower(): int {
        return $this->minPower;
    }
}