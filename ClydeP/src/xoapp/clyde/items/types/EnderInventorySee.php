<?php

namespace xoapp\clyde\items\types;

use pocketmine\item\ItemTypeIds;
use xoapp\clyde\items\CustomItem;
use xoapp\clyde\utils\ItemName;

class EnderInventorySee extends CustomItem
{

    public function __construct()
    {
        parent::__construct(
            "ender_inventory_see",
            ItemName::ENDER_INVENTORY_SEE,
            ItemTypeIds::POTATO
        );
    }
}