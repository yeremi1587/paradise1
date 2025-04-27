<?php

namespace xoapp\clyde\items;

use pocketmine\block\Block;
use pocketmine\block\utils\ColoredTrait;
use pocketmine\block\utils\DyeColor;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CustomItem extends Item
{

    public function __construct(
        string    $nbt_name,
        string    $custom_name,
        int       $id
    )
    {
        parent::__construct(
            new ItemIdentifier($id), TextFormat::clean($custom_name)
        );

        $this->setNamedTag(
            CompoundTag::create()->setString("clyde_item", $nbt_name)
        );

        $this->setCustomName(
            TextFormat::colorize($custom_name)
        );

        $this->setLore(array_map(
            fn (string $line): string => TextFormat::clean($line), []
        ));
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, array &$returnedItems): ItemUseResult
    {
        $this->onClickAir($player, $player->getDirectionVector(), $returnedItems);
        return ItemUseResult::FAIL();
    }
}