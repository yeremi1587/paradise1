<?php

namespace Max\mining\managers;

use pocketmine\world\Position;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as TF;
use Max\mining\Main;
use pocketmine\world\World;

class EventManager {
    private $plugin;
    private $active = false;
    private $eventLocation = null;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->scheduleNextEvent();
    }

    public function scheduleNextEvent(): void {
        $interval = $this->plugin->getConfig()->get("events")["interval"] ?? 3600;
        $this->plugin->getScheduler()->scheduleDelayedTask(new class($this) extends Task {
            private $manager;
            
            public function __construct(EventManager $manager) {
                $this->manager = $manager;
            }
            
            public function onRun(): void {
                $this->manager->startEvent();
            }
        }, $interval * 20);
    }

    public function startEvent(): void {
        if($this->active) return;
        
        $this->active = true;
        $world = $this->plugin->getServer()->getWorldManager()->getWorldByName(
            $this->plugin->getConfig()->get("mining-world", "factions")
        );
        
        if($world instanceof World) {
            $x = mt_rand(-500, 500);
            $z = mt_rand(-500, 500);
            $y = $this->findSafeY($world, $x, $z);
            
            $this->eventLocation = new Position($x, $y, $z, $world);
            
            $duration = $this->plugin->getConfig()->get("events")["duration"] ?? 300;
            $radius = $this->plugin->getConfig()->get("events")["announcement-radius"] ?? 100;
            
            $message = TF::GOLD . "¡Un evento de minería ha comenzado!\n" .
                      TF::WHITE . "Ubicación: x:$x, y:$y, z:$z\n" .
                      TF::YELLOW . "¡Bloques épicos han aparecido!";
            
            foreach($world->getPlayers() as $player) {
                if($player->getPosition()->distance($this->eventLocation) <= $radius) {
                    $player->sendMessage($message);
                }
            }
            
            $this->plugin->getScheduler()->scheduleDelayedTask(new class($this) extends Task {
                private $manager;
                
                public function __construct(EventManager $manager) {
                    $this->manager = $manager;
                }
                
                public function onRun(): void {
                    $this->manager->endEvent();
                }
            }, $duration * 20);
        }
    }

    private function findSafeY(World $world, int $x, int $z): int {
        for($y = 100; $y > 0; $y--) {
            if(!$world->getBlockAt($x, $y, $z)->isSolid()) continue;
            return $y + 1;
        }
        return 64;
    }

    public function endEvent(): void {
        $this->active = false;
        $this->eventLocation = null;
        $this->scheduleNextEvent();
        
        $world = $this->plugin->getServer()->getWorldManager()->getWorldByName(
            $this->plugin->getConfig()->get("mining-world", "factions")
        );
        
        if($world instanceof World) {
            foreach($world->getPlayers() as $player) {
                $player->sendMessage(TF::RED . "¡El evento de minería ha terminado!");
            }
        }
    }

    public function isEventActive(): bool {
        return $this->active;
    }

    public function getEventLocation(): ?Position {
        return $this->eventLocation;
    }
}