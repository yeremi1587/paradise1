<?php

namespace Paradise\Mining;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use onebone\economyapi\EconomyAPI;
use Paradise\Mining\forms\MainForm;
use Paradise\Mining\managers\StatsManager;
use Paradise\Mining\managers\EventManager;

class Main extends PluginBase implements Listener {
    private Config $config;
    private static Main $instance;
    private EconomyAPI $economy;
    private StatsManager $statsManager;
    private EventManager $eventManager;
    private array $placedBlocks = [];

    public function onEnable(): void {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        
        $this->saveDefaultConfig();
        $this->config = $this->getConfig();
        
        $this->statsManager = new StatsManager($this);
        $this->eventManager = new EventManager($this);
        
        $economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        if($economy instanceof EconomyAPI) {
            $this->economy = $economy;
        } else {
            $this->getLogger()->error("EconomyAPI not found! Plugin disabled.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
    }

    public static function getInstance(): Main {
        return self::$instance;
    }

    public function getEconomy(): EconomyAPI {
        return $this->economy;
    }

    public function getStatsManager(): StatsManager {
        return $this->statsManager;
    }

    public function getEventManager(): EventManager {
        return $this->eventManager;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if($command->getName() === "mining") {
            if(!($sender instanceof Player)) {
                $sender->sendMessage("§cThis command can only be used in-game!");
                return true;
            }
            
            $form = new MainForm();
            $sender->sendForm($form);
            return true;
        }
        return false;
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        $block = $event->getBlockAgainst();
        $pos = $block->getPosition();
        $key = "{$pos->getX()},{$pos->getY()},{$pos->getZ()}";
        $this->placedBlocks[$key] = true;
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $pos = $block->getPosition();
        
        // Check if in mining world
        if($player->getWorld()->getFolderName() !== $this->config->get("mining-world", "factions")) {
            return;
        }

        // Check if block was placed by a player
        $blockKey = "{$pos->getX()},{$pos->getY()},{$pos->getZ()}";
        if(isset($this->placedBlocks[$blockKey])) {
            unset($this->placedBlocks[$blockKey]);
            $player->sendPopup("§cYou cannot earn money from placed blocks!");
            return;
        }

        $multiplier = $this->statsManager->getMultiplier($player);
        
        // Check if during event for additional multiplier
        if($this->eventManager->isEventActive()) {
            $eventLoc = $this->eventManager->getEventLocation();
            if($eventLoc !== null && $block->getPosition()->distance($eventLoc) <= 10) {
                $multiplier *= 2;
            }
        }

        // Basic blocks mining rewards
        $basicRewards = [
            VanillaBlocks::STONE()->getTypeId() => 20,
            VanillaBlocks::COBBLESTONE()->getTypeId() => 15,
            VanillaBlocks::GRANITE()->getTypeId() => 20,
            VanillaBlocks::DIORITE()->getTypeId() => 20,
            VanillaBlocks::ANDESITE()->getTypeId() => 20,
            VanillaBlocks::DIRT()->getTypeId() => 10,
            VanillaBlocks::GRAVEL()->getTypeId() => 10
        ];

        if(isset($basicRewards[$block->getTypeId()])) {
            $reward = (int)($basicRewards[$block->getTypeId()] * $multiplier);
            $this->economy->addMoney($player, $reward);
            $this->statsManager->addBlockMined($player, $block->getName());
            $this->statsManager->addCoinsEarned($player, $reward);
            $player->sendPopup("§7Basic Block Mined! §a+$" . $reward);
            return;
        }

        // Special blocks rewards (only for exchange menu)
        $specialRewards = [
            VanillaBlocks::GOLD_ORE()->getTypeId() => 10,
            VanillaBlocks::DIAMOND_ORE()->getTypeId() => 50,
            VanillaBlocks::ANCIENT_DEBRIS()->getTypeId() => 100
        ];

        if(isset($specialRewards[$block->getTypeId()])) {
            $this->statsManager->addBlockMined($player, $block->getName());
            $player->sendPopup("§6Special Block Found! §fCollect it to exchange");
        }
    }
}