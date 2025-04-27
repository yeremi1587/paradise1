<?php

namespace xoapp\clyde\commands;

use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use xoapp\clyde\utils\ClydeUtils;
use pocketmine\command\CommandSender;
use xoapp\clyde\profile\factory\ProfileFactory;

class WarnsCommand extends Command
{

    public function __construct()
    {
        parent::__construct("warns");
        $this->setPermission("warn.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!isset($args[0])) {
            $sender->sendMessage(TextFormat::colorize("&cUse /warns (player)"));
            return;
        }

        $player = ClydeUtils::getPlayerByPrefix($args[0]);
        if (is_null($player)) {
            $sender->sendMessage(TextFormat::colorize("&cThis player is not online"));
            return;
        }

        $warns = ProfileFactory::getProfile($player)?->getWarns() ?? [];
        $sender->sendMessage(TextFormat::colorize(
            "&eAvailable WarnsID (" . sizeof($warns) . "): &7" . implode(", ", array_keys($warns))
        ));
    }
}