<?php

namespace Max\mining;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use onebone\economyapi\EconomyAPI;
use Max\mining\forms\MainForm;
use Max\mining\managers\StatsManager;
use Max\mining\managers\EventManager;

class Main extends PluginBase implements Listener {
    private Config $config;
    private static Main $instance;
    private EconomyAPI $economy;
    private StatsManager $statsManager;
    private EventManager $eventManager;

    public function onEnable(): void {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        
        // Save default config
        $this->saveDefaultConfig();
        $this->config = $this->getConfig();
        
        // Initialize managers
        $this->statsManager = new StatsManager($this);
        $this->eventManager = new EventManager($this);
        
        // Check for EconomyAPI
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
            $form->sendTo($sender);
            return true;
        }
        return false;
    }

    /**
     * Handle block breaking event
     */
    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        
        // Check if in mining world
        if($player->getWorld()->getFolderName() !== $this->config->get("mining-world", "factions")) {
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

        // Basic stone mining reward
        if($block->isSameType(VanillaBlocks::STONE())) {
            $reward = (int)(2 * $multiplier);
            $this->economy->addMoney($player, $reward);
            $this->statsManager->addBlockMined($player, 'stone');
            $this->statsManager->addCoinsEarned($player, $reward);
            $player->sendPopup("§a+$reward coins");
            return;
        }

        // Special blocks rewards
        $rewards = [
            VanillaBlocks::GOLD_ORE()->getTypeId() => 10,
            VanillaBlocks::DIAMOND_ORE()->getTypeId() => 50,
            VanillaBlocks::ANCIENT_DEBRIS()->getTypeId() => 100
        ];

        if(isset($rewards[$block->getTypeId()])) {
            $baseReward = $rewards[$block->getTypeId()];
            $reward = (int)($baseReward * $multiplier);
            $this->economy->addMoney($player, $reward);
            $this->statsManager->addBlockMined($player, $block->getName());
            $this->statsManager->addCoinsEarned($player, $reward);
            $player->sendPopup("§a+$reward coins");
        }
    }
}