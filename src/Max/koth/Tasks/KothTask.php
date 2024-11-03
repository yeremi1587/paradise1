<?php

namespace Max\koth\Tasks;

use Max\koth\Arena;
use Max\koth\KOTH;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class KothTask extends Task {
    private ?Player $king = null;
    private string $kingName = "...";
    private int $captureTime;
    private KOTH $pl;
    private Arena $arena;

    public function __construct(KOTH $pl, Arena $arena) {
        $this->pl = $pl;
        $this->arena = $arena;
        $this->resetKing();
    }

    public function onRun(): void {
        if (isset($this->king) && $this->king->isOnline() && $this->arena->isInside($this->king)) {
            if (time() - $this->captureTime >= $this->pl->config->CAPTURE_TIME) {
                $this->pl->stopKoth($this->kingName);
                return;
            }
        } else {
            $this->resetKing();
        }

        $this->updateDisplays();
    }

    private function resetKing(): void {
        $this->king = null;
        $this->kingName = "...";
        $this->captureTime = time();
        
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        shuffle($onlinePlayers);
        
        foreach ($onlinePlayers as $player) {
            if ($this->arena->isInside($player)) {
                $this->king = $player;
                $this->kingName = $player->getName();
                break;
            }
        }
    }

    private function updateDisplays(): void {
        $timeLeft = $this->pl->config->CAPTURE_TIME - (time() - $this->captureTime);
        $minutes = floor($timeLeft / 60);
        $seconds = sprintf("%02d", ($timeLeft - ($minutes * 60)));

        // Actualizar solo el bossbar si está habilitado
        if ($this->pl->config->USE_BOSSBAR) {
            $this->updateBossBar($minutes, $seconds, $timeLeft);
        }

        // Mostrar solo el actionbar si está habilitado
        if ($this->pl->config->SEND_ACTIONBAR) {
            $this->updateActionBar($minutes, $seconds, $timeLeft);
        }
    }

    private function updateBossBar(int $minutes, string $seconds, int $timeLeft): void {
        $this->pl->bar->setTitle("§uKOTH: §t" . $this->arena->getName() . "§r - §uTime: §t" . $minutes . ":" . $seconds);
        $this->pl->bar->setSubTitle("§uKing: §t" . $this->kingName);
        $this->pl->bar->setPercentage($timeLeft / $this->pl->config->CAPTURE_TIME);
        $this->pl->setBossBarColor((string)$this->pl->config->COLOR_BOSSBAR);
    }

    private function updateActionBar(int $minutes, string $seconds, int $timeLeft): void {
        $barMessage = "§7KOTH: §f" . $this->arena->getName() . " §7Time: §f" . $minutes . ":" . $seconds . "\n§7King: §f" . $this->kingName;
        $timePercentage = 100 * (1 - ($timeLeft / $this->pl->config->CAPTURE_TIME));
        $barText = $this->generateProgressBar($timePercentage);

        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $player->sendActionBarMessage($barMessage . " §r\n" . $barText);
        }
    }

    private function generateProgressBar(float $percentage): string {
        $barLength = 60;
        $filledBlocks = floor($barLength * ($percentage / 100));
        $emptyBlocks = $barLength - $filledBlocks;
        
        return "§7[" . str_repeat("§f|", $filledBlocks) . str_repeat("§8|", $emptyBlocks) . "§7]";
    }
}