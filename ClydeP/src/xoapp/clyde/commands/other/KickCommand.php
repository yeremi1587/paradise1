<?php

namespace xoapp\clyde\commands\other;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use xoapp\clyde\utils\ClydeUtils;
use xoapp\clyde\utils\Prefixes;

class KickCommand extends Command
{

    public function __construct()
    {
        parent::__construct("kick");

        $this->setPermission("kick.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§cUsage: /kick <player> <reason>");
            return;
        }

        if (!isset($args[1])) {
            $sender->sendMessage("§cUsage: /kick <player> <reason>");
            return;
        }

        $i_player = ClydeUtils::getPlayerByPrefix($args[0]);
        if (is_null($i_player)) {
            $sender->sendMessage("§cThis player is not online!");
            return;
        }

        $reason = implode(" ", array_slice($args, 1));
        ClydeUtils::globalMessage(
            Prefixes::GLOBAL . "§a" . $i_player->getName() . " §7has been kicked"
        );

        $i_player->kick("§cYou have been kicked by " . $sender->getName() . " §7for §e" . $reason);
    }
}