<?php

declare(strict_types=1);

namespace xoapp\clyde\library\muqsit\invmenu\type\util\builder;

use xoapp\clyde\library\muqsit\invmenu\type\graphic\network\InvMenuGraphicNetworkTranslator;
use xoapp\clyde\library\muqsit\invmenu\type\graphic\network\MultiInvMenuGraphicNetworkTranslator;
use xoapp\clyde\library\muqsit\invmenu\type\graphic\network\WindowTypeInvMenuGraphicNetworkTranslator;

trait GraphicNetworkTranslatableInvMenuTypeBuilderTrait{

	/** @var InvMenuGraphicNetworkTranslator[] */
	private array $graphic_network_translators = [];

	public function addGraphicNetworkTranslator(InvMenuGraphicNetworkTranslator $translator) : self{
		$this->graphic_network_translators[] = $translator;
		return $this;
	}

	public function setNetworkWindowType(int $window_type) : self{
		$this->addGraphicNetworkTranslator(new WindowTypeInvMenuGraphicNetworkTranslator($window_type));
		return $this;
	}

	protected function getGraphicNetworkTranslator() : ?InvMenuGraphicNetworkTranslator{
		if(count($this->graphic_network_translators) === 0){
			return null;
		}

		if(count($this->graphic_network_translators) === 1){
			return $this->graphic_network_translators[array_key_first($this->graphic_network_translators)];
		}

		return new MultiInvMenuGraphicNetworkTranslator($this->graphic_network_translators);
	}
}