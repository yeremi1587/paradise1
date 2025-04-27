
<?php

namespace xoapp\clyde\commands;

use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use xoapp\clyde\utils\ClydeUtils;
use pocketmine\command\CommandSender;
use xoapp\clyde\profile\factory\ProfileFactory;
use xoapp\clyde\player\PlayerData;

class WarnCommand extends Command
{
    public function __construct()
    {
        parent::__construct("warn", "Warn a player");
        $this->setPermission("warn.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!isset($args[0])) {
            $sender->sendMessage(TextFormat::colorize("&cUse /warn (player) (reason)"));
            return;
        }

        if (!isset($args[1])) {
            $sender->sendMessage(TextFormat::colorize("&cUse /warn (player) (reason)"));
            return;
        }

        $playerName = $args[0];
        $reason = implode(" ", array_slice($args, 1));
        
        // Verificar si el jugador existe en la data
        $playerData = PlayerData::getInstance()->getData($playerName);
        if ($playerData === null) {
            $sender->sendMessage(TextFormat::colorize("&cPlayer not found in database"));
            return;
        }

        // Guardar el warn aunque el jugador esté offline
        ProfileFactory::getProfile($playerName)?->addWarn([
            "reason" => $reason,
            "sender" => $sender->getName(),
            "date" => date("Y-m-d H:i:s")
        ]);

        // Si el jugador está online, notificarle
        $player = ClydeUtils::getPlayerByPrefix($playerName);
        if ($player !== null) {
            $player->sendMessage(TextFormat::colorize(
                "&6You has been warned for &f" . $reason
            ));
        }

        $sender->sendMessage(TextFormat::colorize("&aWarn successfully sent"));
    }
}
