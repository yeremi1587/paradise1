<?php

namespace Max\mining\managers;

use pocketmine\player\Player;
use Max\mining\Main;

class StatsManager {
    private $plugin;
    private $stats = [];

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->loadStats();
    }

    public function loadStats(): void {
        $path = $this->plugin->getDataFolder() . "stats.json";
        if(file_exists($path)) {
            $this->stats = json_decode(file_get_contents($path), true) ?? [];
        }
    }

    public function saveStats(): void {
        $path = $this->plugin->getDataFolder() . "stats.json";
        file_put_contents($path, json_encode($this->stats));
    }

    public function addBlockMined(Player $player, string $blockType): void {
        $name = $player->getName();
        if(!isset($this->stats[$name])) {
            $this->stats[$name] = [
                'blocksMined' => 0,
                'coinsEarned' => 0,
                'specialBlocksFound' => 0,
                'multiplier' => 1
            ];
        }
        
        $this->stats[$name]['blocksMined']++;
        if($blockType !== 'stone') {
            $this->stats[$name]['specialBlocksFound']++;
        }
        $this->saveStats();
    }

    public function addCoinsEarned(Player $player, int $amount): void {
        $name = $player->getName();
        if(isset($this->stats[$name])) {
            $this->stats[$name]['coinsEarned'] += $amount;
            $this->saveStats();
        }
    }

    public function getStats(Player $player): array {
        return $this->stats[$player->getName()] ?? [
            'blocksMined' => 0,
            'coinsEarned' => 0,
            'specialBlocksFound' => 0,
            'multiplier' => 1
        ];
    }

    public function getTopMiners(int $limit = 10): array {
        $stats = $this->stats;
        uasort($stats, function($a, $b) {
            return ($b['coinsEarned'] ?? 0) - ($a['coinsEarned'] ?? 0);
        });
        return array_slice($stats, 0, $limit);
    }

    public function setMultiplier(Player $player, float $multiplier): void {
        $name = $player->getName();
        if(isset($this->stats[$name])) {
            $this->stats[$name]['multiplier'] = $multiplier;
            $this->saveStats();
        }
    }

    public function getMultiplier(Player $player): float {
        return $this->stats[$player->getName()]['multiplier'] ?? 1.0;
    }
}