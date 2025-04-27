<?php

namespace xoapp\clyde\items\types;

use pocketmine\item\ItemTypeIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use xoapp\clyde\forms\TeleportForm;
use xoapp\clyde\items\CustomItem;
use xoapp\clyde\utils\ItemName;

class Teleport extends CustomItem
{

    public function __construct()
    {
        parent::__construct(
            "teleport",
            ItemName::TELEPORT,
            ItemTypeIds::COMPASS
        );
    }

    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems): ItemUseResult
    {
        $player->sendForm(new TeleportForm($player));
        return ItemUseResult::SUCCESS;
    }
}