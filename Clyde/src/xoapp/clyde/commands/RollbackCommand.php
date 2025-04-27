<?php

namespace xoapp\clyde\commands;

use pocketmine\player\Player;
use pocketmine\command\Command;
use xoapp\clyde\utils\ClydeUtils;
use pocketmine\command\CommandSender;
use xoapp\clyde\forms\InventoryManager;
use xoapp\clyde\profile\factory\ProfileFactory;

class RollbackCommand extends Command
{

    public function __construct()
    {
        parent::__construct("rollback");

        $this->setPermission("rollback.command");

        $this->setAliases(
            ["rb"]
        );
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {

        if (!$sender instanceof Player) {
            return;
        }

        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("Usage /rollback <player>");
            return;
        }

        $i_player = ClydeUtils::getPlayerByPrefix($args[0]);
        if (is_null($i_player)) {
            $sender->sendMessage("§cPlayer Not Found");
            return;
        }

        $profile = ProfileFactory::getProfile($i_player);
        if (is_null($profile)) {
            $sender->sendMessage("§cProfile Data Not found, talk to owner");
            return;
        }

        InventoryManager::getProfileData($sender, $profile);
    }
}