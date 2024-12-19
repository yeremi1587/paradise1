<?php

declare(strict_types=1);

namespace Max\koth;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use Max\koth\Commands\kothCommand;
use Max\koth\Config\KothConfig;
use Max\koth\Tasks\KothTask;
use Max\koth\Tasks\StartKothTask;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\types\BossBarColor;
use xenialdan\apibossbar\BossBar;
use CortexPE\Commando\PacketHooker;
use pocketmine\scheduler\TaskHandler;
use pocketmine\console\ConsoleCommandSender;

class KOTH extends PluginBase {
    protected static KOTH $instance;
    private ?TaskHandler $taskHandler = null;
    protected ?Arena $current = null;
    private Config $data;
    protected array $arenas = [];
    public BossBar $bar;
    public KothConfig $config;
    
    public function onEnable(): void {
        self::$instance = $this;

        $this->saveResource("config.yml");
        $this->saveResource("data.yml");
        $configFile = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML);
        
        $this->config = new KothConfig($configFile->getAll());

        // Cargar arenas guardadas
        $this->loadArenas();

        if ($this->config->USE_BOSSBAR) {
            $this->bar = new BossBar();
        }

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

    private function loadArenas(): void {
        foreach ($this->data->getAll() as $name => $arenaData) {
            if (!isset($arenaData["pos1"]) || !isset($arenaData["pos2"]) || !isset($arenaData["pos1"][3]) || !isset($arenaData["pos2"][3])) {
                $this->getLogger()->warning("Arena '$name' has invalid data and will be skipped.");
                continue;
            }

            $world = $this->getServer()->getWorldManager()->getWorldByName($arenaData["pos1"][3]);
            if ($world === null) {
                $this->getLogger()->warning("World '{$arenaData["pos1"][3]}' not found for arena '$name'.");
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
                $this->getLogger()->warning("Failed to load arena '$name': " . $e->getMessage());
                continue;
            }
        }
    }

    public static function getInstance(): KOTH {
        return self::$instance;
    }

    public function setBossBarColor(string $color): void {
        if (!$this->config->USE_BOSSBAR || !isset($this->bar)) {
            return;
        }

        $colorConstant = match (strtolower($color)) {
            "0", "pink" => BossBarColor::PINK,
            "1", "blue" => BossBarColor::BLUE,
            "2", "red" => BossBarColor::RED,
            "3", "green" => BossBarColor::GREEN,
            "4", "yellow" => BossBarColor::YELLOW,
            "5", "purple" => BossBarColor::PURPLE,
            default => BossBarColor::BLUE,
        };
        
        $this->bar->setColor($colorConstant);
    }

    public function getData(): Config {
        return $this->data;
    }

    public function getArenas(): array {
        return $this->arenas;
    }

    public function getArena(string $name): ?Arena {
        return $this->arenas[$name] ?? null;
    }

    public function getCurrentArena(): ?Arena {
        return $this->current;
    }

    public function isRunning(): bool {
        return $this->taskHandler !== null;
    }

    public function createArena(string $name, Position $pos1, Position $pos2): string {
        if (isset($this->arenas[$name])) {
            return "KOTH » §7Ya existe una arena con ese nombre";
        }

        $this->arenas[$name] = new Arena($name, $pos1, $pos2);
        $this->data->set($name, [
            "pos1" => [$pos1->getX(), $pos1->getY(), $pos1->getZ(), $pos1->getWorld()->getFolderName()],
            "pos2" => [$pos2->getX(), $pos2->getY(), $pos2->getZ(), $pos2->getWorld()->getFolderName()]
        ]);
        $this->data->save();

        return "KOTH » §7Arena creada correctamente";
    }

    public function deleteArena(string $name): string {
        if (!isset($this->arenas[$name])) {
            return "KOTH » §7No existe una arena con ese nombre";
        }

        unset($this->arenas[$name]);
        $this->data->remove($name);
        $this->data->save();

        return "KOTH » §7Arena eliminada correctamente";
    }

    public function startKoth(Arena $arena): string {
        if ($this->isRunning()) {
            return "KOTH » §7El KOTH ya está en ejecución";
        }

        $this->taskHandler = $this->getScheduler()->scheduleRepeatingTask(new KothTask($this, $arena), $this->config->TASK_DELAY);
        $this->current = $arena;
        $arenaName = $arena->getName();
        $pos = $arena->getSpawn();
        $coords = round($pos->getX(), 2) . " " . round($pos->getY(), 2) . " " . round($pos->getZ(), 2);

        $message = "KOTH » §7El KOTH ha sido iniciado en §f" . $arenaName . "\n";
        $message .= "§7Coordenadas: §f" . $coords;

        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            if ($this->config->USE_BOSSBAR) {
                $this->bar->addPlayer($player);
            }
            $player->sendMessage($message);
        }

        $this->setBossBarColor($this->config->COLOR_BOSSBAR);

        if ($this->config->USE_WEBHOOK) {
            $webhook = new Webhook($this->config->WEBHOOK_URL);
            $msg = new Message();
            $embed = new Embed();
            $embed->setTitle("KOTH Started");
            $embed->setDescription("A new KOTH event has started at arena " . $arenaName . "\nCoordinates: " . $coords);
            $embed->setColor(0x00ff00);
            $msg->addEmbed($embed);
            $webhook->send($msg);
        }

        return $message;
    }

    public function stopKoth(string $winnerName = null): string {
        if (!$this->isRunning()) {
            return "KOTH » §7No hay ningún evento de KOTH en ejecución";
        }

        if ($winnerName !== null) {
            $winner = $this->getServer()->getPlayerExact($winnerName);
            if ($winner instanceof Player) {
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
                $this->bar->removePlayer($player);
            }
        }

        $this->taskHandler->cancel();
        $this->taskHandler = null;
        $this->current = null;

        if ($this->config->USE_WEBHOOK && $winnerName !== null) {
            $webhook = new Webhook($this->config->WEBHOOK_URL);
            $msg = new Message();
            $embed = new Embed();
            $embed->setTitle("KOTH Ended");
            $embed->setDescription("The KOTH event has ended. Winner: " . $winnerName);
            $embed->setColor(0xff0000);
            $msg->addEmbed($embed);
            $webhook->send($msg);
        }

        return "KOTH » §7El KOTH ha sido detenido";
    }
}

