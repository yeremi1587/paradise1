<?php

namespace xoapp\clyde\handlers;

use xoapp\clyde\items\types\Punishment;
use xoapp\clyde\forms\punishment\BanForm;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use xoapp\clyde\forms\InventoryManager;
use xoapp\clyde\items\types\EnderInventorySee;
use xoapp\clyde\items\types\Freeze;
use xoapp\clyde\items\types\InventorySee;
use xoapp\clyde\items\types\PlayerInfo;
use xoapp\clyde\session\Session;
use xoapp\clyde\session\SessionFactory;
use xoapp\clyde\utils\ClydeUtils;
use xoapp\clyde\utils\Prefixes;

class ItemHandler implements Listener
{

    public function onEntityDamage(EntityDamageByEntityEvent $event): void
    {
        $i_player = $event->getEntity();
        $player = $event->getDamager();

        if (!$i_player instanceof Player) {
            return;
        }

        if (!$player instanceof Player) {
            return;
        }

        $session = SessionFactory::getInstance()->get($player);
        if (is_null($session)) {
            return;
        }

        $event->cancel();

        $item = $player->getInventory()->getItemInHand();

        $i_session = SessionFactory::getInstance()->get($i_player);
        if ($i_session instanceof Session) {
            return;
        }

        if ($item instanceof Freeze) {

            $f_session = SessionFactory::getInstance()->getFreeze($i_player);
            if (!is_null($f_session)) {

                $f_session->close();
                SessionFactory::getInstance()->unregisterFreeze($i_player);

                ClydeUtils::globalMessage(
                    Prefixes::GLOBAL . "The player §e" . $i_player->getName() . " §7has been unfrozen by §a" . $player->getName()
                );
                return;
            }

            SessionFactory::getInstance()->registerFreeze($i_player);
            ClydeUtils::globalMessage(
                Prefixes::GLOBAL . "The player §e" . $i_player->getName() . " §7has been frozen by §a" . $player->getName()
            );

            return;
        }


        if ($item instanceof PlayerInfo) {
            ClydeUtils::sendInformationMessage($player, $i_player);
            return;
        }

        if ($item instanceof Punishment) {
            $player->sendForm(new BanForm($i_player));
            return;
        }

        if ($item instanceof InventorySee) {
            InventoryManager::openInventory($player, $i_player);
            return;
        }

        if ($item instanceof EnderInventorySee) {
            InventoryManager::openEnderInventory($player, $i_player);
        }
    }

    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        $nbt = $item->getNamedTag()->getTag("clyde_item");
        if (is_null($nbt)) {
            return;
        }

        $session = SessionFactory::getInstance()->get($player);
        if (!is_null($session)) {
            return;
        }

        $player->getInventory()->removeItem($item);
        $player->sendMessage("§cThis item is prohibited to be used by other players.");
        $event->cancel();
    }
}