<?php

namespace xoapp\clyde\commands\other;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use xoapp\clyde\data\PermanentlyData;
use xoapp\clyde\data\TemporarilyData;

class UnBanCommand extends Command
{

    public function __construct()
    {
        parent::__construct("unban");

        $this->setPermission("unban.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§cUsage: /unban <player>");
            return;
        }

        $p_data = PermanentlyData::getInstance();
        $t_data = TemporarilyData::getInstance();

        if ($p_data->exists($args[0])) {
            $p_data->removeData($args[0]);
            $sender->sendMessage("§aUnbanned §e" . $args[0]);
            return;
        }

        if ($t_data->exists($args[0])) {
            $t_data->removeData($args[0]);
            $sender->sendMessage("§aUnbanned §e" . $args[0]);
            return;
        }

        $sender->sendMessage("§cThis player is not banned!");
    }
}