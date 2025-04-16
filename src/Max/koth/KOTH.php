
<?php

declare(strict_types=1);

namespace Max\koth;

use Max\koth\Config\KothConfig;
use Max\koth\Tasks\KothTask;
use Max\koth\Tasks\StartKothTask;
use Max\koth\Managers\BossBarManager;
use Max\koth\Managers\WebhookManager;
use Max\koth\Managers\ArenaManager;
use Max\koth\Commands\kothCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\scheduler\TaskHandler;
use pocketmine\console\ConsoleCommandSender;
use CortexPE\Commando\PacketHooker;

class KOTH extends PluginBase {
    protected static KOTH $instance;
    private ?TaskHandler $taskHandler = null;
    protected ?Arena $current = null;
    private Config $data;
    public KothConfig $config;
    private BossBarManager $bossBarManager;
    private WebhookManager $webhookManager;
    private ArenaManager $arenaManager;
    
    public function onEnable(): void {
        self::$instance = $this;

        $this->saveResource("config.yml");
        $this->saveResource("data.yml");
        $configFile = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML);
        
        $this->config = new KothConfig($configFile->getAll());
        
        // Initialize managers
        $this->bossBarManager = new BossBarManager($this);
        $this->webhookManager = new WebhookManager($this);
        $this->arenaManager = new ArenaManager($this);

        $this->getServer()->getPluginManager()->registerEvents(
            new Listeners\CommandProtectionListener($this),
            $this
        );

        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        $this->getServer()->getCommandMap()->register("koth", new kothCommand($this, "koth", "KOTH commands prefix"));

        $this->getScheduler()->scheduleRepeatingTask(new StartKothTask($this), 600);
    }

    public static function getInstance(): KOTH {
        return self::$instance;
    }

    public function getData(): Config {
        return $this->data;
    }

    public function getArenaManager(): ArenaManager {
        return $this->arenaManager;
    }

    public function getBossBarManager(): BossBarManager {
        return $this->bossBarManager;
    }

    public function getWebhookManager(): WebhookManager {
        return $this->webhookManager;
    }

    public function getArenas(): array {
        return $this->arenaManager->getArenas();
    }

    public function getArena(string $name): ?Arena {
        return $this->arenaManager->getArena($name);
    }

    public function getCurrentArena(): ?Arena {
        return $this->current;
    }

    public function isRunning(): bool {
        return $this->taskHandler !== null;
    }

    public function startKoth(Arena $arena): string {
        if ($this->isRunning()) {
            return "KOTH » KOTH is already running";
        }

        $this->taskHandler = $this->getScheduler()->scheduleRepeatingTask(new KothTask($this, $arena), $this->config->TASK_DELAY);
        $this->current = $arena;
        $arenaName = $arena->getName();
        $pos = $arena->getSpawn();
        $coords = round($pos->getX(), 2) . " " . round($pos->getY(), 2) . " " . round($pos->getZ(), 2);

        $message = "KOTH » KOTH has started in §f" . $arenaName . "\n";
        $message .= "§7Coordinates: §f" . $coords;

        if ($this->config->USE_BOSSBAR) {
            $this->bossBarManager->initializeBossBar($arenaName);
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                $this->bossBarManager->addPlayer($player);
            }
        }

        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $player->sendMessage($message);
        }

        $this->webhookManager->sendKothStartMessage($arenaName, $coords);

        return $message;
    }

    public function stopKoth(string $winnerName = null): string {
        if (!$this->isRunning()) {
            return "KOTH » No KOTH event is currently running";
        }

        if ($winnerName !== null) {
            $winner = $this->getServer()->getPlayerExact($winnerName);
            if ($winner !== null) {
                foreach ($this->config->REWARDS as $command) {
                    $this->getServer()->dispatchCommand(
                        new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()),
                        str_replace("{player}", $winnerName, $command)
                    );
                }
            }
        }

        if ($this->config->USE_BOSSBAR) {
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                $this->bossBarManager->removePlayer($player);
            }
        }

        $this->taskHandler->cancel();
        $this->taskHandler = null;
        $this->current = null;

        if ($winnerName !== null) {
            $this->webhookManager->sendKothEndMessage($winnerName);
        }

        return "KOTH » The KOTH has been stopped";
    }
}
