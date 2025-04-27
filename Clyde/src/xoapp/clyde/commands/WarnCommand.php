<?php

namespace xoapp\clyde\commands;

use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use xoapp\clyde\utils\ClydeUtils;
use pocketmine\command\CommandSender;
use xoapp\clyde\profile\factory\ProfileFactory;

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

        $player = ClydeUtils::getPlayerByPrefix($args[0]);
        if (is_null($player)) {
            $sender->sendMessage(TextFormat::colorize("&cThis player is not online"));
            return;
        }

        $reason = implode(" ", array_slice($args, 1));
        ProfileFactory::getProfile($player)?->addWarn([
            "reason" => $reason,
            "sender" => $sender->getName(),
            "date" => date("Y-m-d H:i:s")
        ]);

        $player->sendMessage(TextFormat::colorize(
            "&6You has been warned for &f" . $reason
        ));

        $sender->sendMessage(TextFormat::colorize("&aWarn successfully send"));
    }
}