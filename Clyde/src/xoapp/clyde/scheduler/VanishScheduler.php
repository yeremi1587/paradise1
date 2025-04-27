<?php

namespace xoapp\clyde\scheduler;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\scheduler\Task;
use xoapp\clyde\session\Session;

class VanishScheduler extends Task
{

    public function __construct(
        private readonly Session $session
    )
    {
    }

    public function onRun(): void
    {
        $player = $this->session->getPlayer();
        if (!$player->isOnline()) {
            $this->getHandler()->cancel();
            return;
        }

        if (!$this->session->isVanished()) {
            $this->getHandler()->cancel();
            return;
        }

        $player->getEffects()->add(
            new EffectInstance(VanillaEffects::NIGHT_VISION(), 100, 1, false)
        );
    }
}