<?php

namespace xoapp\clyde\commands\other;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use xoapp\clyde\data\MuteData;
use xoapp\clyde\formatter\TimeFormatter;
use xoapp\clyde\utils\ClydeUtils;
use xoapp\clyde\utils\Prefixes;

class MuteCommand extends Command
{

    public function __construct()
    {

        parent::__construct("mute");

        $this->setPermission("mute.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§cUsage: /mute <player> <time> <reason>");
            return;
        }

        if (!isset($args[1])) {
            $sender->sendMessage("§cUsage: /mute <player> <time> <reason>");
            return;
        }

        if (!isset($args[2])) {
            $sender->sendMessage("§cUsage: /mute <player> <time> <reason>");
            return;
        }

        if (!TimeFormatter::isValidFormat($args[1])) {
            $sender->sendMessage("§cSet a valid time format!");
            return;
        }

        $data = MuteData::getInstance();

        $i_player = ClydeUtils::getPlayerByPrefix($args[0]);
        $reason = implode(" ", array_slice($args, 2));
        $time = TimeFormatter::parseTime($args[1]);

        if (is_null($i_player)) {

            if ($data->exists($args[0])) {
                $sender->sendMessage("§cThis player is already muted!");
                return;
            }

            $data->setData(
                $args[0], [
                    "duration" => $time,
                    "reason" => $reason,
                    "date" => date("Y-m-d H:i:s"),
                    "sender" => $sender->getName()
                ]
            );

            ClydeUtils::globalMessage(
                Prefixes::GLOBAL . "§a" . $args[0] . " §7has been muted"
            );
            return;
        }

        if ($data->exists($i_player->getName())) {
            $sender->sendMessage("§cThis player is already muted!");
            return;
        }

        $data->setData(
            $i_player->getName(), [
                "duration" => $time,
                "reason" => $reason,
                "date" => date("Y-m-d H:i:s"),
                "sender" => $sender->getName()
            ]
        );

        ClydeUtils::globalMessage(
            Prefixes::GLOBAL . "§a" . $i_player->getName() . " §7has been muted"
        );

        $i_player->sendMessage(
            Prefixes::GLOBAL . "You have been muted by §e" . $sender->getName() . " §7for §e" . $reason
        );
    }
}