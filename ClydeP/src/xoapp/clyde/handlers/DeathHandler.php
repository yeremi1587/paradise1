<?php

namespace xoapp\clyde\handlers;

use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use xoapp\clyde\profile\factory\ProfileFactory;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class DeathHandler implements Listener
{

    public function onPlayerDeath(PlayerDeathEvent $event): void
    {
        $player = $event->getPlayer();
        $cause = $player->getLastDamageCause();
        $i_player = null;

        if ($cause instanceof EntityDamageByEntityEvent) {

            $i_player = $cause->getDamager();
            if (!$i_player instanceof Player) {
                $i_player = null;
            }
        }

        $profile = ProfileFactory::getProfile($player);
        if (is_null($profile)) {
            return;
        }

        $profile->addLog(
            [
                "contents" => $player->getInventory()->getContents(),
                "armor" => $player->getArmorInventory()->getContents(),
                "offhand" => $player->getOffHandInventory()->getContents(),
                "killer" => is_null($i_player) ? "Unknown" : $i_player->getName(),
                "date" => date("Y-m-d H:i:s")
            ]
        );
    }
}