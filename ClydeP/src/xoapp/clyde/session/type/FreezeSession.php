<?php

namespace xoapp\clyde\session\type;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;
use pocketmine\Server;

readonly class FreezeSession
{

    public function __construct(
        private string $name
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPlayer(): ?Player
    {
        return Server::getInstance()->getPlayerExact($this->name);
    }

    public function update(): void
    {
        $this->getPlayer()?->getEffects()->add(
            new EffectInstance(VanillaEffects::BLINDNESS(), null, 3)
        );

        $this->getPlayer()?->sendActionBarMessage("§cYou are frozen!");
    }

    public function close(): void
    {
        $this->getPlayer()?->getEffects()->clear();
    }
}