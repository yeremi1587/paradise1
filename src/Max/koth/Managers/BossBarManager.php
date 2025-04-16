
<?php

declare(strict_types=1);

namespace Max\koth\Managers;

use Max\koth\KOTH;
use pocketmine\player\Player;
use xenialdan\apibossbar\BossBar;
use pocketmine\network\mcpe\protocol\types\BossBarColor;

class BossBarManager {
    private ?BossBar $bar = null;
    private KOTH $plugin;

    public function __construct(KOTH $plugin) {
        $this->plugin = $plugin;
    }

    public function getBossBar(): ?BossBar {
        return $this->bar;
    }

    public function initializeBossBar(string $arenaName): void {
        if (!$this->plugin->config->USE_BOSSBAR) {
            return;
        }

        $this->bar = new BossBar();
        $this->setBossBarColor($this->plugin->config->COLOR_BOSSBAR);
        
        $this->bar->setTitle("§uKOTH: §t" . $arenaName);
        $this->bar->setSubTitle("§uKing: §t...");
        $this->bar->setPercentage(1.0);
    }

    public function setBossBarColor(string $color): void {
        if (!$this->plugin->config->USE_BOSSBAR || !isset($this->bar) || $this->bar === null) {
            return;
        }

        $colorConstant = match (strtolower($color)) {
            "0", "pink" => BossBarColor::PINK,
            "1", "blue" => BossBarColor::BLUE,
            "2", "red" => BossBarColor::RED,
            "3", "green" => BossBarColor::GREEN,
            "4", "yellow" => BossBarColor::YELLOW,
            "5", "purple" => BossBarColor::PURPLE,
            default => BossBarColor::BLUE,
        };
        
        $this->bar->setColor($colorConstant);
    }

    public function addPlayer(Player $player): void {
        if ($this->bar !== null) {
            $this->bar->addPlayer($player);
        }
    }

    public function removePlayer(Player $player): void {
        if ($this->bar !== null) {
            $this->bar->removePlayer($player);
        }
    }

    public function updateDisplay(string $arenaName, string $kingName, float $percentage): void {
        if ($this->bar === null) {
            return;
        }

        $this->bar->setTitle("§uKOTH: §t" . $arenaName);
        $this->bar->setSubTitle("§uKing: §t" . $kingName);
        $this->bar->setPercentage($percentage);
    }
}
