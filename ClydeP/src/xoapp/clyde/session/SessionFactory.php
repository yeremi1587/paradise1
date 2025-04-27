<?php

namespace xoapp\clyde\session;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use xoapp\clyde\session\type\FreezeSession;

class SessionFactory
{
    use SingletonTrait {
        setInstance as private;
        reset as private;
    }

    /** @var Session[] */
    private array $sessions = [];

    /** @var FreezeSession[] */
    private array $freeze_sessions = [];

    public function __construct()
    {
        self::setInstance($this);
    }

    public function register(Player $player): void
    {
        $this->sessions[$player->getName()] = new Session($player);
    }

    public function unregister(Player $player): void
    {
        unset($this->sessions[$player->getName()]);
    }

    public function get(Player $player): ?Session
    {
        return $this->sessions[$player->getName()] ?? null;
    }

    /**
     * @return Session[]
     */
    public function getSessions(): array
    {
        return $this->sessions;
    }

    /**
     * @return FreezeSession[]
     */
    public function getFreezeSessions(): array
    {
        return $this->freeze_sessions;
    }

    public function registerFreeze(Player $player): void
    {
        $this->freeze_sessions[$player->getName()] = new FreezeSession($player->getName());
    }

    public function unregisterFreeze(Player $player): void
    {
        unset($this->freeze_sessions[$player->getName()]);
    }

    public function getFreeze(Player $player): ?FreezeSession
    {
        return $this->freeze_sessions[$player->getName()] ?? null;
    }

    public function closeAll(): void
    {
        foreach ($this->sessions as $session) {
            $session->close();
        }
    }
}