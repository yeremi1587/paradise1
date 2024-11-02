<?php

declare(strict_types=1);

namespace Max\koth;

use pocketmine\world\Position;
use pocketmine\player\Player;

class Arena {
    private string $name;
    private ?Position $pos1;
    private ?Position $pos2;
    private ?Position $spawn = null;

    public function __construct(string $name, ?Position $pos1 = null, ?Position $pos2 = null) {
        $this->name = $name;
        $this->pos1 = $pos1;
        $this->pos2 = $pos2;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getPosition1(): ?Position {
        return $this->pos1;
    }

    public function getPosition2(): ?Position {
        return $this->pos2;
    }

    public function setMin(Position $position): void {
        $this->pos1 = $position;
    }

    public function setMax(Position $position): void {
        $this->pos2 = $position;
    }

    public function setSpawn(Position $position): void {
        $this->spawn = $position;
        
        // Guardar el spawn en data.yml
        $plugin = KOTH::getInstance();
        $data = $plugin->getData();
        $arenaData = $data->get($this->name, []);
        $arenaData["spawn"] = [$position->getX(), $position->getY(), $position->getZ(), $position->getWorld()->getFolderName()];
        $data->set($this->name, $arenaData);
        $data->save();
    }

    public function removeSpawn(): void {
        $this->spawn = null;
    }

    public function isInside(Player $player): bool {
        if ($this->pos1 === null || $this->pos2 === null) {
            return false;
        }

        $playerPosition = $player->getPosition();
        $minX = min($this->pos1->getX(), $this->pos2->getX());
        $maxX = max($this->pos1->getX(), $this->pos2->getX());
        $minY = min($this->pos1->getY(), $this->pos2->getY());
        $maxY = max($this->pos1->getY(), $this->pos2->getY());
        $minZ = min($this->pos1->getZ(), $this->pos2->getZ());
        $maxZ = max($this->pos1->getZ(), $this->pos2->getZ());

        return ($playerPosition->getX() >= $minX && $playerPosition->getX() <= $maxX
            && $playerPosition->getY() >= $minY && $playerPosition->getY() <= $maxY
            && $playerPosition->getZ() >= $minZ && $playerPosition->getZ() <= $maxZ);
    }

    public function getSpawn(): ?Position {
        return $this->spawn;
    }
}