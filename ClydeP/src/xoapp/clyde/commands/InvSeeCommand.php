<?php

namespace xoapp\clyde\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use xoapp\clyde\forms\InventoryManager;
use xoapp\clyde\utils\ClydeUtils;

class InvSeeCommand extends Command
{

    public function __construct()
    {
        parent::__construct("invsee");

        $this->setPermission("staffmode.command");
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
            $sender->sendMessage("§cUsage: /invsee (end : normal) <player>");
            return;
        }

        if (!isset($args[1])) {
            $sender->sendMessage("§cUsage: /invsee (end : normal) <player>");
            return;
        }

        $i_player = ClydeUtils::getPlayerByPrefix($args[1]);
        if (!$i_player instanceof Player) {
            $sender->sendMessage("§cPlayer not found");
            return;
        }

        switch (strtolower($args[0])) {
            case "end":
            {
                InventoryManager::openEnderInventory($sender, $i_player);
                return;
            }

            case "normal":
            {
                InventoryManager::openInventory($sender, $i_player);
                return;
            }

            default:
            {
                $sender->sendMessage("§cUsage: /invsee (end : normal) <player>");
                return;
            }
        }
    }
}