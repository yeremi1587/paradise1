<?php

namespace xoapp\clyde\commands;

use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use xoapp\clyde\utils\ClydeUtils;
use pocketmine\command\CommandSender;
use xoapp\clyde\profile\factory\ProfileFactory;

class WarnInfoCommand extends Command
{

    public function __construct()
    {
        parent::__construct("warninfo");
        $this->setPermission("warn.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!isset($args[0])) {
            $sender->sendMessage(TextFormat::colorize("&cUse /warninfo (player) (id)"));
            return;
        }

        if (!isset($args[1])) {
            $sender->sendMessage(TextFormat::colorize("&cUse /warninfo (player) (id)"));
            return;
        }

        $player = ClydeUtils::getPlayerByPrefix($args[0]);
        if (is_null($player)) {
            $sender->sendMessage(TextFormat::colorize("&cThis player is not online"));
            return;
        }

        if (!is_numeric($args[1])) {
            $sender->sendMessage(TextFormat::colorize("&cThis warnID must be a number"));
            return;
        }

        $warn = ProfileFactory::getProfile($player)->getWarn($args[1]);
        if (is_null($warn)) {
            $sender->sendMessage(TextFormat::colorize("&cThis warnID does not exist"));
            return;
        }

        $message = [
            "&l&a   WARN DATA&r  ",
            " &ePlayer: &f" . $player->getName(),
            " &eReason: &f" . $warn["reason"],
            " &eWarn Date: &f" . $warn["date"],
            " "
        ];

        $sender->sendMessage(TextFormat::colorize(
            implode("\n", $message)
        ));
    }
}