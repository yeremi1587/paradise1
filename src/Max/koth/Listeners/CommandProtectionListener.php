<?php

declare(strict_types=1);

namespace Max\koth\Listeners;

use Max\koth\KOTH;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;

class CommandProtectionListener implements Listener {
    private KOTH $plugin;
    private array $blockedCommands = ["f", "faction", "factions"];

    public function __construct(KOTH $plugin) {
        $this->plugin = $plugin;
    }

    public function onCommand(CommandEvent $event): void {
        $sender = $event->getSender();
        if (!($sender instanceof Player)) {
            return;
        }

        if (!$this->plugin->isRunning()) {
            return;
        }

        $command = strtolower($event->getCommand());
        $commandParts = explode(" ", $command);
        $baseCommand = $commandParts[0];

        foreach ($this->blockedCommands as $blockedCmd) {
            if ($baseCommand === $blockedCmd) {
                $event->cancel();
                $sender->sendMessage("§c(§8RaveKOTH§c) §7No puedes usar este comando durante el KOTH.");
                break;
            }
        }

        // Manejo especial para el comando "f claim"
        if ($baseCommand === "f" && isset($commandParts[1]) && strtolower($commandParts[1]) === "claim") {
            $event->cancel();
            $sender->sendMessage("§c(§8RaveKOTH§c) §7No puedes usar este comando durante el KOTH.");
        }
    }
}