
<?php

declare(strict_types=1);

namespace Max\koth\Managers;

use Max\koth\Arena;
use Max\koth\KOTH;
use pocketmine\world\Position;

class ArenaManager {
    private array $arenas = [];
    private KOTH $plugin;

    public function __construct(KOTH $plugin) {
        $this->plugin = $plugin;
        $this->loadArenas();
    }

    public function getArenas(): array {
        return $this->arenas;
    }

    public function getArena(string $name): ?Arena {
        return $this->arenas[$name] ?? null;
    }

    private function loadArenas(): void {
        foreach ($this->plugin->getData()->getAll() as $name => $arenaData) {
            if (!isset($arenaData["pos1"]) || !isset($arenaData["pos2"]) || !isset($arenaData["pos1"][3]) || !isset($arenaData["pos2"][3])) {
                $this->plugin->getLogger()->warning("Arena '$name' has invalid data and will be skipped.");
                continue;
            }

            $world = $this->plugin->getServer()->getWorldManager()->getWorldByName($arenaData["pos1"][3]);
            if ($world === null) {
                $this->plugin->getLogger()->warning("World '{$arenaData["pos1"][3]}' not found for arena '$name'.");
                continue;
            }

            try {
                $pos1 = new Position($arenaData["pos1"][0], $arenaData["pos1"][1], $arenaData["pos1"][2], $world);
                $pos2 = new Position($arenaData["pos2"][0], $arenaData["pos2"][1], $arenaData["pos2"][2], $world);
                
                $this->arenas[$name] = new Arena($name, $pos1, $pos2);
                
                if (isset($arenaData["spawn"]) && is_array($arenaData["spawn"]) && count($arenaData["spawn"]) >= 4) {
                    $spawn = new Position($arenaData["spawn"][0], $arenaData["spawn"][1], $arenaData["spawn"][2], $world);
                    $this->arenas[$name]->setSpawn($spawn);
                }
            } catch (\Throwable $e) {
                $this->plugin->getLogger()->warning("Failed to load arena '$name': " . $e->getMessage());
                continue;
            }
        }
    }

    public function createArena(string $name, Position $pos1, Position $pos2): string {
        if (isset($this->arenas[$name])) {
            return "KOTH » An arena with that name already exists";
        }

        $this->arenas[$name] = new Arena($name, $pos1, $pos2);
        $this->plugin->getData()->set($name, [
            "pos1" => [$pos1->getX(), $pos1->getY(), $pos1->getZ(), $pos1->getWorld()->getFolderName()],
            "pos2" => [$pos2->getX(), $pos2->getY(), $pos2->getZ(), $pos2->getWorld()->getFolderName()]
        ]);
        $this->plugin->getData()->save();

        return "KOTH » Arena created successfully";
    }

    public function deleteArena(string $name): string {
        if (!isset($this->arenas[$name])) {
            return "KOTH » An arena with that name doesn't exist";
        }

        unset($this->arenas[$name]);
        $this->plugin->getData()->remove($name);
        $this->plugin->getData()->save();

        return "KOTH » Arena deleted successfully";
    }
}
