<?php

declare(strict_types=1);

namespace xoapp\clyde\library\muqsit\invmenu\type;

use pocketmine\block\Block;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use xoapp\clyde\library\muqsit\invmenu\inventory\InvMenuInventory;
use xoapp\clyde\library\muqsit\invmenu\InvMenu;
use xoapp\clyde\library\muqsit\invmenu\type\graphic\BlockInvMenuGraphic;
use xoapp\clyde\library\muqsit\invmenu\type\graphic\InvMenuGraphic;
use xoapp\clyde\library\muqsit\invmenu\type\graphic\network\InvMenuGraphicNetworkTranslator;
use xoapp\clyde\library\muqsit\invmenu\type\util\InvMenuTypeHelper;

final class BlockFixedInvMenuType implements FixedInvMenuType{

	public function __construct(
		readonly private Block $block,
		readonly private int $size,
		readonly private ?InvMenuGraphicNetworkTranslator $network_translator = null
	){}

	public function getSize() : int{
		return $this->size;
	}

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic{
		$origin = $player->getPosition()->addVector(InvMenuTypeHelper::getBehindPositionOffset($player))->floor();
		if(!InvMenuTypeHelper::isValidYCoordinate($origin->y)){
			return null;
		}

		return new BlockInvMenuGraphic($this->block, $origin, $this->network_translator);
	}

	public function createInventory() : Inventory{
		return new InvMenuInventory($this->size);
	}
}