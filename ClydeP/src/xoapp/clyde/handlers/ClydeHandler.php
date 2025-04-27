<?php

namespace xoapp\clyde\handlers;

use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use xoapp\clyde\Loader;
use xoapp\clyde\session\SessionFactory;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;

class ClydeHandler implements Listener
{

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();

        $session = SessionFactory::getInstance()->get($player);
        if (is_null($session)) {
            return;
        }

        $session->close();
        SessionFactory::getInstance()->unregister($player);
    }

    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();

        $session = SessionFactory::getInstance()->get($player);
        $f_session = SessionFactory::getInstance()->getFreeze($player);

        if (!is_null($f_session)) {
            $event->cancel();
        }

        if (is_null($session)) {
            return;
        }

        $event->cancel();
    }

    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();

        $session = SessionFactory::getInstance()->get($player);
        $f_session = SessionFactory::getInstance()->getFreeze($player);

        if (!is_null($f_session)) {
            $event->cancel();
        }

        if (is_null($session)) {
            return;
        }

        $event->cancel();
    }

    public function onInventoryTransaction(InventoryTransactionEvent $event): void
    {
        $player = $event->getTransaction()->getSource();

        $f_session = SessionFactory::getInstance()->getFreeze($player);

        if (is_null($f_session)) {
            return;
        }

        $event->cancel();
    }

    public function onPlayerExhaust(PlayerExhaustEvent $event): void
    {
        $player = $event->getPlayer();

        if (!$player instanceof Player) {
            return;
        }

        $session = SessionFactory::getInstance()->get($player);
        $f_session = SessionFactory::getInstance()->getFreeze($player);

        if (!is_null($f_session)) {
            $event->cancel();
        }

        if (is_null($session)) {
            return;
        }

        $event->cancel();
    }

    public function onPlayerDrop(PlayerDropItemEvent $event): void
    {
        $player = $event->getPlayer();

        $session = SessionFactory::getInstance()->get($player);
        $f_session = SessionFactory::getInstance()->getFreeze($player);

        if (!is_null($f_session)) {
            $event->cancel();
        }

        if (is_null($session)) {
            return;
        }

        $event->cancel();
    }

    public function onEntityPickup(EntityItemPickupEvent $event): void
    {
        $player = $event->getEntity();

        if (!$player instanceof Player) {
            return;
        }

        $session = SessionFactory::getInstance()->get($player);
        $f_session = SessionFactory::getInstance()->getFreeze($player);

        if (!is_null($f_session)) {
            $event->cancel();
        }

        if (is_null($session)) {
            return;
        }

        $event->cancel();
    }

    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();

        if (!$player instanceof Player) {
            return;
        }

        $session = SessionFactory::getInstance()->get($player);
        $f_session = SessionFactory::getInstance()->getFreeze($player);

        if (!is_null($f_session)) {
            $event->cancel();
        }

        if (is_null($session)) {
            return;
        }

        $event->cancel();
    }

    public function onPlayerChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        $message = $event->getMessage();

        $session = SessionFactory::getInstance()->get($player);
        if (is_null($session) || !$session->isStaffChat()) {
            if (!Loader::getInstance()->isMutedChat()) {
                return;
            }

            $player->sendMessage(TextFormat::colorize("&cGlobal Chat is Muted"));
            $event->cancel();
            return;
        }

        $event->cancel();

        $sessions = SessionFactory::getInstance()->getSessions();
        foreach ($sessions as $iSession) {
            $iSession->getPlayer()?->sendMessage(TextFormat::colorize(
                "&8(&6StaffChat&8) &7" . $player->getName() . ": &f" . $message
            ));
        }
    }
}