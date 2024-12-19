<?php

declare(strict_types=1);

namespace Max\koth\Commands\SubCommands;

use CortexPE\Commando\BaseSubCommand;
use Max\koth\KOTH;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use CortexPE\Commando\args\RawStringArgument;

class startSubCommand extends BaseSubCommand {
    protected function prepare(): void {
        $this->registerArgument(0, new RawStringArgument("arena", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cEste comando solo puede ser usado por jugadores");
            return;
        }

        if (!isset($args["arena"])) {
            $sender->sendMessage("§cUso: /koth start <nombre_arena>");
            $sender->sendMessage("§7Usa /koth list para ver las arenas disponibles");
            return;
        }

        $plugin = KOTH::getInstance();
        $arena = $plugin->getArena($args["arena"]);

        if ($arena === null) {
            $sender->sendMessage("§cLa arena especificada no existe");
            return;
        }

        $sender->sendMessage($plugin->startKoth($arena));
    }
}