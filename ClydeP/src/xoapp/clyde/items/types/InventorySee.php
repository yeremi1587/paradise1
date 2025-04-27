<?php

namespace xoapp\clyde\items\types;

use pocketmine\item\ItemTypeIds;
use xoapp\clyde\items\CustomItem;
use xoapp\clyde\utils\ItemName;

class InventorySee extends CustomItem
{

    public function __construct()
    {
        parent::__construct(
            "inventory_see",
            ItemName::INVENTORY_SEE,
            ItemTypeIds::COOKIE
        );
    }
}