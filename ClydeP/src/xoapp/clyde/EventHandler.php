<?php

namespace xoapp\clyde;

use xoapp\clyde\profile\data\ProfileData;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerLoginEvent;
use xoapp\clyde\profile\factory\ProfileFactory;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use xoapp\clyde\data\MuteData;
use xoapp\clyde\data\PermanentlyData;
use xoapp\clyde\data\TemporarilyData;
use xoapp\clyde\formatter\TimeFormatter;
use xoapp\clyde\player\PlayerData;
use xoapp\clyde\scheduler\async\GetCountryAsync;
use xoapp\clyde\session\SessionFactory;
use xoapp\clyde\utils\ClydeUtils;

class EventHandler implements Listener
{

    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();

        if (!PlayerData::getInstance()->exists($player)) {
            PlayerData::getInstance()->register($player);
        }

        if (!ProfileData::exists($player->getName())) {
            ProfileData::setData($player->getName(), []);
        }

        ClydeUtils::hideSessions($player);

        ProfileFactory::register($player);

        Server::getInstance()->getAsyncPool()->submitTask(new GetCountryAsync(
            $player->getName(), $player->getNetworkSession()->getIp()
        ));
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $f_session = SessionFactory::getInstance()->getFreeze($player);

        $profile = ProfileFactory::getProfile($player);
        if (!is_null($profile)) {
            $profile->save();
            ProfileFactory::unregister($player);
        }
    }

    public function onPlayerChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();

        $data = MuteData::getInstance();
        if (!$data->exists($player->getName())) {
            return;
        }

        $m_data = $data->getData($player->getName());
        $time_left = TimeFormatter::getTimeLeft($m_data["duration"]);

        if ($time_left <= 0) {
            $data->removeData($player->getName());
            return;
        }

        $message = [
            "§l§c    MUTED    ",
            " ",
            " §fReason: §7" . $m_data["reason"],
            " §fTime left: §7" . $time_left,
            " §fSender: §7" . $m_data["sender"],
            " §f",
        ];

        $player->sendMessage(
            implode("\n", $message)
        );

        $event->cancel();
    }

    public function onPlayerLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();

        $username = $player->getPlayerInfo()->getUsername();
        $address = $player->getNetworkSession()->getIp();

        $pe_data = PermanentlyData::getInstance();
        $te_data = TemporarilyData::getInstance();

        if ($pe_data->exists($address) || $pe_data->exists($username)) {
            $data = $pe_data->exists($address) ?
                $pe_data->getData($address) :
                $pe_data->getData($username);

            if (!is_array($data)) {
                return;
            }

            $flag_message = [
                "§cYou Are Banned! &3Appeal in Discord §r",
                "§fReason: §7" . $data["reason"],
                "§fSender: §7" . $data["sender"]
            ];

            $player->kick(
                implode("\n", $flag_message)
            );
            return;
        }

        if ($te_data->exists($address) || $te_data->exists($username)) {
            $data = $te_data->exists($address) ?
                $te_data->getData($address) :
                $te_data->getData($username);

            if (!is_array($data)) {
                return;
            }

            $duration = TimeFormatter::getTimeLeft($data["duration"]);
            if ($duration <= 0) {
                if ($te_data->exists($username)) {
                    $te_data->removeData($username);
                } else {
                    $te_data->removeData($address);
                }
                return;
            }

            $flag_message = [
                "§cYou Are Banned! &3Appeal in Discord §r",
                "§fReason: §7" . $data["reason"],
                "§fSender: §7" . $data["sender"],
                "§fDuration: §e" . $duration
            ];

            $player->kick(
                implode("\n", $flag_message)
            );
        }
    }

    public function onCommand(CommandEvent $event): void
    {
        $player = $event->getSender();
        if (!$player instanceof Player) {
            return;
        }

        $f_session = SessionFactory::getInstance()->getFreeze($player);
        if (is_null($f_session)) {
            return;
        }

        $player->sendMessage("§l§cYou are frozen!");
        $event->cancel();
    }

    public function onPlayerMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();

        $f_session = SessionFactory::getInstance()->getFreeze($player);
        if (is_null($f_session)) {
            return;
        }

        $event->cancel();
    }

    public function onInventory(InventoryTransactionEvent $event): void
    {
        $player = $event->getTransaction()->getSource();

        $f_session = SessionFactory::getInstance()->getFreeze($player);
        if (is_null($f_session)) {
            return;
        }

        $event->cancel();
    }

    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();

        $f_session = SessionFactory::getInstance()->getFreeze($player);
        if (is_null($f_session)) {
            return;
        }

        $event->cancel();
    }

    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();

        $f_session = SessionFactory::getInstance()->getFreeze($player);
        if (is_null($f_session)) {
            return;
        }

        $event->cancel();
    }
}