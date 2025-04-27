<?php

namespace xoapp\clyde\items\types;

use pocketmine\item\ItemTypeIds;
use xoapp\clyde\items\CustomItem;
use xoapp\clyde\utils\ItemName;

class PlayerInfo extends CustomItem
{

    public function __construct()
    {
        parent::__construct(
            "playerinfo",
            ItemName::PLAYER_INFO,
            ItemTypeIds::STICK
        );
    }
}