<?php

namespace xoapp\clyde\commands\other;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use xoapp\clyde\player\PlayerData;
use xoapp\clyde\utils\ClydeUtils;

class AltsCommand extends Command
{

    public function __construct()
    {
        parent::__construct("alts");

        $this->setPermission("alts.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§cUsage: /alts <player>");
            return;
        }

        $i_player = ClydeUtils::getPlayerByPrefix($args[0]);
        if (is_null($i_player)) {

            $alts = PlayerData::getInstance()->getPossibleAlts($args[0]);
            if (sizeof($alts) <= 0) {
                $sender->sendMessage("§c" . $args[0] . " has no possible alts.");
                return;
            }

            $format = implode(", ", $alts);
            $sender->sendMessage("§c" . $args[0] . " Possible Alts: §c" . $format);
            return;
        }

        $alts = PlayerData::getInstance()->getPossibleAlts($i_player->getName());
        if (sizeof($alts) <= 0) {
            $sender->sendMessage("§c" . $i_player->getName() . " has no possible alts.");
            return;
        }

        $format = implode(", ", $alts);
        $sender->sendMessage("§a" . $i_player->getName() . " Possible Alts: §c" . $format);
    }
}