<?php

namespace xoapp\clyde\commands\other;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use xoapp\clyde\data\MuteData;

class UnMuteCommand extends Command
{


    public function __construct()
    {
        parent::__construct("unmute");

        $this->setPermission("unmute.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§cUsage: /unmute <player>");
            return;
        }

        $data = MuteData::getInstance();
        if (!$data->exists($args[0])) {
            $sender->sendMessage("§cThis player is not muted!");
            return;
        }

        $data->removeData($args[0]);
        $sender->sendMessage("§aUnMuted §e" . $args[0]);
    }
}