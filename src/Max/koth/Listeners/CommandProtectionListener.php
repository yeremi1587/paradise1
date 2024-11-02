<?php

declare(strict_types=1);

namespace Max\koth\Listeners;

use Max\koth\KOTH;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class CommandProtectionListener implements Listener {
    private KOTH $plugin;
    private array $blockedCommands = ["/f", "/faction", "/factions", "/f claim"];

    public function __construct(KOTH $plugin) {
        $this->plugin = $plugin;
    }

    public function onCommand(PlayerCommandPreprocessEvent $event): void {
        if (!$this->plugin->isRunning()) {
            return;
        }

        $player = $event->getPlayer();
        $command = strtolower($event->getMessage());

        foreach ($this->blockedCommands as $blockedCmd) {
            if (str_starts_with($command, $blockedCmd)) {
                $event->cancel();
                $player->sendMessage("§c(§8RaveKOTH§c) §7No puedes usar este comando durante el KOTH.");
                break;
            }
        }
    }
}