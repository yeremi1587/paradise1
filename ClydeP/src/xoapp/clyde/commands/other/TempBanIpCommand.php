<?php

namespace xoapp\clyde\commands\other;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use xoapp\clyde\data\TemporarilyData;
use xoapp\clyde\formatter\TimeFormatter;
use xoapp\clyde\utils\ClydeUtils;
use xoapp\clyde\utils\Prefixes;
use xoapp\clyde\profile\factory\ProfileFactory;

class TempBanIpCommand extends Command
{

    public function __construct()
    {
        parent::__construct("tempban-ip");

        $this->setPermission("tempban-ip.command");

        $this->setAliases(
            ["tban-ip"]
        );
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§cUsage: /tempban <player> <time> <reason>");
            return;
        }

        if (!isset($args[1])) {
            $sender->sendMessage("§cUsage: /tempban <player> <time> <reason>");
            return;
        }

        if (!isset($args[2])) {
            $sender->sendMessage("§cUsage: /tempban <player> <time> <reason>");
            return;
        }

        $i_player = ClydeUtils::getPlayerByPrefix($args[0]);
        if (!TimeFormatter::isValidFormat($args[1])) {
            $sender->sendMessage("§cSet a valid time format!");
            return;
        }

        $time = TimeFormatter::parseTime($args[1]);
        $reason = implode(" ", array_slice($args, 2));
        $data = TemporarilyData::getInstance();

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
                "duration" => $time,
                "reason" => $reason,
                "date" => date("Y-m-d H:i:s"),
                "sender" => $sender->getName()
            ]);

            $username = ClydeUtils::getPlayerByAddress($args[0]) ?? "Unknown";

            ClydeUtils::globalMessage(
                Prefixes::GLOBAL . "§e" . $username . "§7 has been banned for §c" . $reason
            );
            return;
        }

        $data->setData($i_player->getNetworkSession()->getIp(), [
            "duration" => $time,
            "reason" => $reason,
            "date" => date("Y-m-d H:i:s"),
            "sender" => $sender->getName()
        ]);

        ClydeUtils::globalMessage(
            Prefixes::GLOBAL . "§e" . $i_player->getName() . "§7 has been banned for §c" . $reason
        );

        $i_player->kick("§cYou have been banned by §f" . $sender->getName());
    }
}