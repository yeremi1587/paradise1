<?php

namespace xoapp\clyde\commands\other;

use pocketmine\command\Command;
use xoapp\clyde\utils\ClydeUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use xoapp\clyde\data\PermanentlyData;
use xoapp\clyde\data\TemporarilyData;

class BanListCommand extends Command
{

    public function __construct()
    {
        parent::__construct("banlist");

        $this->setPermission("banlist.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§cUsage: /banlist <permanently : temporarily>");
            return;
        }

        if ($args[0] == "permanently") {
            $users_banned = ClydeUtils::getBannedUsers(true);
            $format = implode(", ", $users_banned);

            $address_banned = ClydeUtils::getBannedAddress(true);
            $address_format = implode(", ", $address_banned);

            $sender->sendMessage("§fPermanently Banned: §7" . $format);
            $sender->sendMessage("§fPermanently Banned Addresses: §7" . $address_format);
            return;
        }

        $users_banned = ClydeUtils::getBannedUsers();
        $format = implode(", ", $users_banned);

        $address_banned = ClydeUtils::getBannedAddress();
        $address_format = implode(", ", $address_banned);

        $sender->sendMessage("§fTemporarily Banned: §7" . $format);
        $sender->sendMessage("§fTemporarily Banned Addresses: §7" . $address_format);
    }
}