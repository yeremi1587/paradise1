<?php

namespace xoapp\clyde\commands\other;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use xoapp\clyde\data\PermanentlyData;
use xoapp\clyde\utils\ClydeUtils;
use xoapp\clyde\utils\Prefixes;

class BanIpCommand extends Command
{

    public function __construct()
    {
        parent::__construct("ban-ip");

        $this->setPermission("ban-ip.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§cUsage: /ban-ip <player> <reason>");
            return;
        }

        if (!isset($args[1])) {
            $sender->sendMessage("§cUsage: /ban-ip <player> <reason>");
            return;
        }

        $i_player = ClydeUtils::getPlayerByPrefix($args[0]);
        $reason = implode(" ", array_slice($args, 1));
        $data = PermanentlyData::getInstance();

        if (is_null($i_player)) {

            if (!filter_var($args[0], FILTER_VALIDATE_IP)) {
                $sender->sendMessage("§cPlease put a valid ip address");
                return;
            }

            if ($data->exists($args[0])) {
                $sender->sendMessage("§cThis player is already banned!");
                return;
            }

            $data->setData($args[0], [
                "date" => date("Y-m-d H:i:s"),
                "reason" => $reason,
                "sender" => $sender->getName()
            ]);

            $username = ClydeUtils::getPlayerByAddress($args[0]) ?? "Unknown";
            ClydeUtils::globalMessage(
                Prefixes::GLOBAL . "§a" . $username . " §7has been banned by §f" . $sender->getName() . " §7for §e" . $args[1]
            );

            return;
        }

        $data->setData($i_player->getNetworkSession()->getIp(), [
            "date" => date("Y-m-d H:i:s"),
            "reason" => $reason,
            "sender" => $sender->getName()
        ]);

        ClydeUtils::globalMessage(
            Prefixes::GLOBAL . "§a" . $i_player->getName() . " §7has been banned by §f" . $sender->getName() . " §7for §e" . $args[1]
        );

        $i_player->kick("§cYou have been banned by §f" . $sender->getName());
    }
}