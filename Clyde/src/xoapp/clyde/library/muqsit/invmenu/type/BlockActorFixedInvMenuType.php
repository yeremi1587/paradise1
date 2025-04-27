<?php

declare(strict_types=1);

namespace xoapp\clyde\library\muqsit\invmenu\type;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\math\Facing;
use pocketmine\player\Player;
use xoapp\clyde\library\muqsit\invmenu\inventory\InvMenuInventory;
use xoapp\clyde\library\muqsit\invmenu\InvMenu;
use xoapp\clyde\library\muqsit\invmenu\type\graphic\BlockActorInvMenuGraphic;
use xoapp\clyde\library\muqsit\invmenu\type\graphic\BlockInvMenuGraphic;
use xoapp\clyde\library\muqsit\invmenu\type\graphic\InvMenuGraphic;
use xoapp\clyde\library\muqsit\invmenu\type\graphic\MultiBlockInvMenuGraphic;
use xoapp\clyde\library\muqsit\invmenu\type\graphic\network\InvMenuGraphicNetworkTranslator;
use xoapp\clyde\library\muqsit\invmenu\type\util\InvMenuTypeHelper;
use function count;

final class BlockActorFixedInvMenuType implements FixedInvMenuType{

	public function __construct(
		readonly private Block $block,
		readonly private int $size,
		readonly private string $tile_id,
		readonly private ?InvMenuGraphicNetworkTranslator $network_translator = null,
		readonly private int $animation_duration = 0
	){}

	public function getSize() : int{
		return $this->size;
	}

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic{
		$position = $player->getPosition();
		$origin = $position->addVector(InvMenuTypeHelper::getBehindPositionOffset($player))->floor();
		if(!InvMenuTypeHelper::isValidYCoordinate($origin->y)){
			return null;
		}

		$graphics = [new BlockActorInvMenuGraphic($this->block, $origin, BlockActorInvMenuGraphic::createTile($this->tile_id, $menu->getName()), $this->network_translator, $this->animation_duration)];
		foreach(InvMenuTypeHelper::findConnectedBlocks("Chest", $position->getWorld(), $origin, Facing::HORIZONTAL) as $side){
			$graphics[] = new BlockInvMenuGraphic(VanillaBlocks::BARRIER(), $side);
		}

		return count($graphics) > 1 ? new MultiBlockInvMenuGraphic($graphics) : $graphics[0];
	}

	public function createInventory() : Inventory{
		return new InvMenuInventory($this->size);
	}
}