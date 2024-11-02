<?php

declare(strict_types=1);

namespace Max\koth\Commands\SubCommands;

use CortexPE\Commando\BaseSubCommand;
use Max\koth\KOTH;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class listSubCommand extends BaseSubCommand {
    private KOTH $plugin;

    public function __construct(string $name, string $description = "", array $aliases = []) {
        $this->plugin = KOTH::getInstance();
        parent::__construct($name, $description, $aliases);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        $arenas = $this->plugin->getArenas();
        if (empty($arenas)) {
            $sender->sendMessage("§c(§8RaveKOTH§c) §7No hay arenas creadas");
            return;
        }

        $message = "§c(§8RaveKOTH§c) §7Arenas disponibles:\n";
        foreach ($arenas as $name => $arena) {
            $message .= "§7- §f" . $name . "\n";
        }
        $sender->sendMessage($message);
    }

    protected function prepare(): void {
        // No additional preparation needed
    }
}