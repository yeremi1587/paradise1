
<?php

namespace xoapp\clyde\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use xoapp\clyde\utils\ClydeUtils;
use xoapp\clyde\utils\Prefixes;
use xoapp\clyde\player\PlayerData;

class PlayerIpCommand extends Command
{
    public function __construct()
    {
        parent::__construct("playerip");
        $this->setPermission("playerip.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage(Prefixes::GLOBAL . "§cUso: /playerip <jugador>");
            return;
        }

        $target = ClydeUtils::getPlayerByPrefix($args[0]);
        if (is_null($target)) {
            $data = PlayerData::getInstance()->getData($args[0]);
            if ($data === false) {
                $sender->sendMessage(Prefixes::GLOBAL . "§cJugador no encontrado.");
                return;
            }
            $sender->sendMessage(Prefixes::GLOBAL . "§7IP de " . $args[0] . ": §f" . $data["address"]);
            return;
        }

        $sender->sendMessage(Prefixes::GLOBAL . "§7IP de " . $target->getName() . ": §f" . $target->getNetworkSession()->getIp());
    }
}
