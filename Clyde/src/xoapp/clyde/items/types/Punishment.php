<?php

namespace xoapp\clyde\items\types;

use pocketmine\item\ItemTypeIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use xoapp\clyde\forms\TeleportForm;
use xoapp\clyde\items\CustomItem;
use xoapp\clyde\utils\ItemName;

class Punishment extends CustomItem
{

    public function __construct()
    {
        parent::__construct(
            "punishment",
            ItemName::PUNISHMENT,
            ItemTypeIds::BLAZE_ROD
        );
    }
}