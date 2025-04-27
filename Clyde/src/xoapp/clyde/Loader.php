<?php

namespace xoapp\clyde;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use xoapp\clyde\commands\MuteChatCommand;
use xoapp\clyde\handlers\ItemHandler;
use xoapp\clyde\commands\ModCommand;
use xoapp\clyde\commands\WarnCommand;
use xoapp\clyde\handlers\DeathHandler;
use xoapp\clyde\commands\WarnsCommand;
use xoapp\clyde\commands\InvSeeCommand;
use xoapp\clyde\handlers\ClydeHandler;
use xoapp\clyde\commands\FreezeCommand;
use xoapp\clyde\session\SessionFactory;
use xoapp\clyde\commands\RollbackCommand;
use xoapp\clyde\profile\data\ProfileData;
use xoapp\clyde\commands\WarnInfoCommand;
use xoapp\clyde\commands\other\BanCommand;
use xoapp\clyde\scheduler\FreezeScheduler;
use xoapp\clyde\commands\StaffChatCommand;
use xoapp\clyde\commands\other\KickCommand;
use xoapp\clyde\commands\other\AltsCommand;
use xoapp\clyde\commands\other\MuteCommand;
use xoapp\clyde\commands\other\UnBanCommand;
use xoapp\clyde\commands\other\BanIpCommand;
use xoapp\clyde\commands\other\UnMuteCommand;
use xoapp\clyde\commands\other\BanListCommand;
use xoapp\clyde\commands\other\TempBanCommand;
use xoapp\clyde\profile\factory\ProfileFactory;
use xoapp\clyde\commands\other\TempBanIpCommand;
use xoapp\clyde\library\muqsit\invmenu\InvMenuHandler;
use xoapp\clyde\commands\other\PlayerIpCommand;

class Loader extends PluginBase
{
    use SingletonTrait {
        setInstance as private;
        reset as private;
    }

    private bool $mutedChat = false;

    protected function onEnable(): void
    {
        date_default_timezone_set("Mexico/General");

        self::setInstance($this);

        $this->getServer()->getPluginManager()->registerEvents(
            new ClydeHandler(), $this
        );
        $this->getServer()->getPluginManager()->registerEvents(
            new ItemHandler(), $this
        );
        $this->getServer()->getPluginManager()->registerEvents(
            new DeathHandler(), $this
        );
        $this->getServer()->getPluginManager()->registerEvents(
            new EventHandler(), $this
        );

        $this->unregisterCommands(
            ["ban", "ban-ip", "unban-ip", "kick", "unban", "banlist"]
        );

        ProfileData::load();

        $this->getServer()->getCommandMap()->registerAll(
            "clyde", [
                new ModCommand(),
                new PlayerIpCommand(),
                new RollbackCommand(),
                new BanCommand(),
                new KickCommand(),
                new UnBanCommand(),
                new AltsCommand(),
                new MuteCommand(),
                new UnMuteCommand(),
                new BanListCommand(),
                new FreezeCommand(),
                new TempBanCommand(),
                new BanIpCommand(),
                new TempBanIpCommand(),
                new WarnCommand(),
                new WarnInfoCommand(),
                new WarnsCommand(),
                new StaffChatCommand(),
                new MuteChatCommand()
            ]
        );

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

        $this->getScheduler()->scheduleRepeatingTask(new FreezeScheduler(), 20);
    }

    private function unregisterCommands(array $keys): void
    {
        $map = $this->getServer()->getCommandMap();
        foreach ($keys as $key) {

            $command = $map->getCommand($key);
            if (is_null($command)) {
                continue;
            }

            $map->unregister($command);
        }
    }

    protected function onDisable(): void
    {
        ProfileFactory::saveAll();
        SessionFactory::getInstance()->closeAll();
    }

    public function isMutedChat(): bool
    {
        return $this->mutedChat;
    }

    public function setMutedChat(bool $mutedChat): void
    {
        $this->mutedChat = $mutedChat;
    }
}
