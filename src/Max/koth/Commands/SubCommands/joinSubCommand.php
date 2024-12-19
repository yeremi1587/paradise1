<?php

declare(strict_types=1);

namespace Max\koth\Commands\SubCommands;

use CortexPE\Commando\BaseSubCommand;
use Max\koth\KOTH;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class joinSubCommand extends BaseSubCommand {
    protected function prepare(): void {
        // No arguments needed
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cEste comando solo puede ser usado por jugadores");
            return;
        }

        $plugin = KOTH::getInstance();
        $currentArena = $plugin->getCurrentArena();

        if ($currentArena === null) {
            $sender->sendMessage("§cNo hay ningún KOTH activo en este momento");
            return;
        }

        $spawn = $currentArena->getSpawn();
        if ($spawn === null) {
            $sender->sendMessage("§cEl punto de aparición no ha sido configurado para esta arena");
            return;
        }

        $sender->teleport($spawn);
        $sender->sendMessage("§aTe has teletransportado al KOTH");
    }
}