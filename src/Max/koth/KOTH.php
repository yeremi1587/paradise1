<?php

declare(strict_types=1);

namespace Max\koth;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use Max\koth\Commands\kothCommand;
use Max\koth\Tasks\KothTask;
use Max\koth\Tasks\StartKothTask;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\network\mcpe\protocol\types\BossBarColor;
use xenialdan\apibossbar\BossBar;
use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use CortexPE\Commando\PacketHooker;

class KOTH extends PluginBase {
    private static KOTH $instance;
    private ?KothTask $task = null;
    private ?Arena $current = null;
    private Config $data;
    private array $arenas = [];
    public BossBar $bar;
    
    public int $TASK_DELAY = 20;
    public int $CAPTURE_TIME = 300;
    public bool $USE_BOSSBAR = true;
    public string $COLOR_BOSSBAR = "blue";
    public bool $SEND_TIPS = true;
    public bool $SEND_ACTIONBAR = true;
    public bool $USE_WEBHOOK = false;
    public string $WEBHOOK_URL = "";
    public array $REWARDS = [];

    public function onEnable(): void {
        self::$instance = $this;

        $this->saveResource("config.yml");
        $this->saveResource("data.yml");
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML);

        $this->TASK_DELAY = $config->get("task-delay", 20);
        $this->CAPTURE_TIME = $config->get("capture-time", 300);
        $this->USE_BOSSBAR = $config->get("use-bossbar", true);
        $this->COLOR_BOSSBAR = $config->get("color-bossbar", "blue");
        $this->SEND_TIPS = $config->get("send-tips", true);
        $this->SEND_ACTIONBAR = $config->get("send-actionbar", true);
        $this->USE_WEBHOOK = $config->get("use-webhook", false);
        $this->WEBHOOK_URL = $config->get("webhook-url", "");
        $this->REWARDS = $config->get("rewards", []);

        if ($this->USE_BOSSBAR) {
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

    public static function getInstance(): KOTH {
        return self::$instance;
    }

    public function setBossBarColor(string $color): void {
        if (!$this->USE_BOSSBAR || !isset($this->bar)) {
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
        return $this->task !== null;
    }

    public function createArena(string $name, Position $pos1, Position $pos2): string {
        if (isset($this->arenas[$name])) {
            return "§c(§8RaveKOTH§c) §7Ya existe una arena con ese nombre";
        }

        $this->arenas[$name] = new Arena($name, $pos1, $pos2);
        $this->data->set($name, [
            "pos1" => [$pos1->x, $pos1->y, $pos1->z, $pos1->getWorld()->getFolderName()],
            "pos2" => [$pos2->x, $pos2->y, $pos2->z, $pos2->getWorld()->getFolderName()]
        ]);
        $this->data->save();

        return "§c(§8RaveKOTH§c) §7Arena creada correctamente";
    }

    public function deleteArena(string $name): string {
        if (!isset($this->arenas[$name])) {
            return "§c(§8RaveKOTH§c) §7No existe una arena con ese nombre";
        }

        unset($this->arenas[$name]);
        $this->data->remove($name);
        $this->data->save();

        return "§c(§8RaveKOTH§c) §7Arena eliminada correctamente";
    }

    public function startKoth(Arena $arena): string {
        if ($this->isRunning()) {
            return "§c(§8RaveKOTH§c) §7El KOTH ya está en ejecución";
        }

        $this->task = $this->getScheduler()->scheduleRepeatingTask(new KothTask($this, $arena), $this->TASK_DELAY);
        $this->current = $arena;
        $arenaName = $arena->getName();

        if ($this->USE_BOSSBAR) {
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                $this->bar->addPlayer($player);
            }
            $this->setBossBarColor((string)$this->COLOR_BOSSBAR);
        }

        if ($this->USE_WEBHOOK) {
            $webhook = new Webhook($this->WEBHOOK_URL);
            $msg = new Message();
            $embed = new Embed();
            $embed->setTitle("KOTH Started");
            $embed->setDescription("A new KOTH event has started at arena " . $arenaName);
            $embed->setColor(0x00ff00);
            $msg->addEmbed($embed);
            $webhook->send($msg);
        }

        return "§c(§8RaveKOTH§c) §7El KOTH ha sido iniciado";
    }

    public function stopKoth(string $winnerName = null): string {
        if (!$this->isRunning()) {
            return "§c(§8RaveKOTH§c) §7No hay ningún evento de KOTH en ejecución";
        }

        if ($winnerName !== null) {
            $winner = $this->getServer()->getPlayerExact($winnerName);
            if ($winner instanceof Player) {
                foreach ($this->REWARDS as $command) {
                    $this->getServer()->dispatchCommand(
                        $this->getServer()->getConsoleSender(),
                        str_replace("{player}", $winnerName, $command)
                    );
                }
            }
        }

        if ($this->USE_BOSSBAR) {
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                $this->bar->removePlayer($player);
            }
        }

        $this->task->cancel();
        $this->task = null;
        $this->current = null;

        if ($this->USE_WEBHOOK && $winnerName !== null) {
            $webhook = new Webhook($this->WEBHOOK_URL);
            $msg = new Message();
            $embed = new Embed();
            $embed->setTitle("KOTH Ended");
            $embed->setDescription("The KOTH event has ended. Winner: " . $winnerName);
            $embed->setColor(0xff0000);
            $msg->addEmbed($embed);
            $webhook->send($msg);
        }

        return "§c(§8RaveKOTH§c) §7El KOTH ha sido detenido";
    }

    public function onTagResolve(TagsResolveEvent $event) {
        $tag = $event->getTag();
        $tags = explode('.', $tag->getName(), 2);
        $value = "";

        if ($tags[0] !== 'koth' || count($tags) < 2) {
            return;
        }

        switch ($tags[1]) {
            case "running":
                $value = $this->isRunning() ? "Yes" : "No";
                break;
            case "arena":
                $value = $this->current ? $this->current->getName() : "None";
                break;
        }

        $tag->setValue($value);
    }
}
