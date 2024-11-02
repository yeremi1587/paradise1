<?php

declare(strict_types=1);

namespace Max\koth\Tasks;

use Max\koth\KOTH;
use pocketmine\scheduler\Task;

class StartKothTask extends Task {
    private KOTH $pl;

    public function __construct(KOTH $pl) {
        $this->pl = $pl;
    }

    public function onRun(): void {
        if (in_array((float)date("G.i"), $this->pl->config->START_TIMES)) {
            $arenas = $this->pl->getArenas();
            if (!empty($arenas)) {
                $randomArena = $arenas[array_rand($arenas)];
                $this->pl->startKoth($randomArena);
            }
        }
    }
}