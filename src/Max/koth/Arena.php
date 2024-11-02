<?php

declare(strict_types=1);

namespace Max\koth;

use pocketmine\world\Position;
use pocketmine\player\Player;

class Arena {
    private string $name;
    private Position $pos1;
    private Position $pos2;
    private ?Position $spawn = null;

    public function __construct(string $name, Position $pos1 = null, Position $pos2 = null) {
        $this->name = $name;
        $this->pos1 = $pos1;
        $this->pos2 = $pos2;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getPosition1(): Position {
        return $this->pos1;
    }

    public function getPosition2(): Position {
        return $this->pos2;
    }

    public function setSpawn(Position $position): void {
        $this->spawn = $position;
    }

    public function removeSpawn(): void {
        $this->spawn = null;
    }

    public function isInside(Player $player): bool {
        $playerPosition = $player->getPosition();
        return ($playerPosition->getX() >= $this->pos1->getX() && $playerPosition->getX() <= $this->pos2->getX()
            && $playerPosition->getY() >= $this->pos1->getY() && $playerPosition->getY() <= $this->pos2->getY()
            && $playerPosition->getZ() >= $this->pos1->getZ() && $playerPosition->getZ() <= $this->pos2->getZ());
    }

    public function getSpawn(): ?Position {
        return $this->spawn;
    }
}
