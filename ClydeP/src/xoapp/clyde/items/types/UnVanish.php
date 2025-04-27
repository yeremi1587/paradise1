<?php

namespace xoapp\clyde\items\types;

use pocketmine\block\utils\DyeColor;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use xoapp\clyde\items\CustomItem;
use xoapp\clyde\session\SessionFactory;
use xoapp\clyde\utils\ClydeUtils;
use xoapp\clyde\utils\ItemName;

class UnVanish extends CustomItem
{

    public function __construct()
    {
        parent::__construct(
            "un_vanish",
            ItemName::UN_VANISH,
            ItemTypeIds::GLASS_BOTTLE
        );
    }

    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems): ItemUseResult
    {
        $session = SessionFactory::getInstance()->get($player);
        if (is_null($session)) {
            return ItemUseResult::NONE();
        }

        if (!$session->isVanished()) {
            return ItemUseResult::NONE();
        }

        $session->hideOtherStaff();
        $session->setVanish(false);

        ClydeUtils::showToPlayers($player);

        $player->getInventory()->setItem(8, new Vanish());
        $player->getEffects()->clear();

        return ItemUseResult::SUCCESS();
    }
}