<?php

namespace xoapp\clyde\items\types;

use pocketmine\item\ItemTypeIds;
use xoapp\clyde\items\CustomItem;
use xoapp\clyde\utils\ItemName;

class Freeze extends CustomItem
{
    public function __construct()
    {
        parent::__construct(
            "freeze",
            ItemName::FREEZE,
            ItemTypeIds::BAMBOO
        );
    }
}