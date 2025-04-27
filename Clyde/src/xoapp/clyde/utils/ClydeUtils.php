<?php

namespace xoapp\clyde\utils;

use pocketmine\player\Player;
use pocketmine\Server;
use xoapp\clyde\player\PlayerData;
use xoapp\clyde\data\PermanentlyData;
use xoapp\clyde\data\TemporarilyData;
use xoapp\clyde\session\SessionFactory;

class ClydeUtils
{

    /**
     * @return Player[]
     */
    public static function getPlayers(): array
    {
        return Server::getInstance()->getOnlinePlayers();
    }

    public static function getPlayerExact(string $name): ?Player
    {
        return Server::getInstance()->getPlayerExact($name);
    }

    public static function getPlayerByPrefix(string $name): ?Player
    {
        $found = null;
        $name = strtolower($name);
        $delta = PHP_INT_MAX;

        foreach (self::getPlayers() as $player) {
            if (stripos($player->getName(), $name) === 0) {

                $curDelta = strlen($player->getName()) - strlen($name);
                if ($curDelta < $delta) {
                    $found = $player;
                    $delta = $curDelta;
                }

                if ($curDelta <= 0) {
                    break;
                }
            }
        }
        return $found;
    }

    public static function globalMessage(string $message): void
    {
        Server::getInstance()->broadcastMessage($message);
    }

    public static function sendInformationMessage(Player $player, Player $i_player): void
    {
        $i_data = PlayerData::getInstance()->getData($i_player);
        $messages = [
            "    §l§aPLAYER INFORMATION    ",
            " §f",
            " §fUsername: §7" . $i_player->getName(),
            " §fAddress: §7" . $i_player->getNetworkSession()->getIp(),
            " §fXuid: §7" . $i_player->getXuid(),
            " §fDeviceID: §7" . $i_player->getPlayerInfo()->getExtraData()["DeviceModel"],
            " §f",
            " §fFirst login: §7" . $i_data["first_login"],
            " §fCountry: §7" . $i_data["country"],
            " §fCity: §7" . $i_data["city"],
            " §f"
        ];

        foreach ($messages as $message) {
            $player->sendMessage($message);
        }
    }

    public static function hideSessions(Player $player): void
    {
        $sessions = SessionFactory::getInstance()->getSessions();
        foreach ($sessions as $session) {

            if (!$session->isVanished()) {
                continue;
            }

            $i_player = $session->getPlayer();
            if (!$i_player->isOnline()) {
                continue;
            }

            $player->hidePlayer($i_player);
        }
    }

    public static function hideToPlayers(Player $player): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $players) {
            $players->hidePlayer($player);
        }
    }

    public static function showToPlayers(Player $player): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $players) {
            $players->showPlayer($player);
        }
    }

    public static function getPlayersWithAlts(): array
    {
        $players = [];
        foreach (self::getPlayers() as $player) {

            $alts = PlayerData::getInstance()->getPossibleAlts($player->getName());
            if (sizeof($alts) <= 0) {
                continue;
            }

            $players[] = $player->getName();
        }

        return $players;
    }

    public static function getPlayerByAddress(string $address): ?string
    {
        $players = PlayerData::getInstance()->getSavedData();
        $found = null;

        foreach ($players as $key => $data) {
            if (!hash_equals($data["address"], $address)) {
                continue;
            }

            $found = $key;
        }

        return $found;
    }

    public static function getBannedUsers(bool $permanently = false): array
    {
        $data = $permanently ?
            PermanentlyData::getInstance() :
            TemporarilyData::getInstance();

        return array_filter(
            $data->getSavedData(), fn (string $key) => !filter_var($key, FILTER_VALIDATE_IP)
        );
    }

    public static function getBannedAddress(bool $permanently = false): array
    {
        $data = $permanently ?
            PermanentlyData::getInstance() :
            TemporarilyData::getInstance();

        return array_filter(
            $data->getSavedData(), fn (string $key) => filter_var($key, FILTER_VALIDATE_IP)
        );
    }
}