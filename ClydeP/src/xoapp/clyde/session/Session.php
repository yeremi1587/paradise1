<?php

namespace xoapp\clyde\session;

use pocketmine\player\Player;
use pocketmine\world\Position;
use xoapp\clyde\items\types\Punishment;
use xoapp\clyde\items\types\EnderInventorySee;
use xoapp\clyde\items\types\Freeze;
use xoapp\clyde\items\types\InventorySee;
use xoapp\clyde\items\types\PlayerInfo;
use xoapp\clyde\items\types\Teleport;
use xoapp\clyde\items\types\Vanish;
use xoapp\clyde\Loader;
use xoapp\clyde\scheduler\VanishScheduler;
use xoapp\clyde\utils\ClydeUtils;

class Session
{

    private array $items;
    private array $armor;

    private array $offhand;

    private bool $vanish = false;
    private bool $staffchat = false;

    private Position $first;

    public function __construct(
        private readonly Player $player,
    )
    {
        $this->items = [];
        $this->armor = [];
        $this->offhand = [];

        $this->first = $this->player->getPosition();

        $this->initialize();
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function initialize(): void
    {
        $inventory = $this->player->getInventory();
        $a_inventory = $this->player->getInventory();
        $o_inventory = $this->player->getInventory();

        $this->items = $inventory->getContents();
        $this->armor = $a_inventory->getContents();
        $this->offhand = $o_inventory->getContents();

        $inventory->clearAll();
        $a_inventory->clearAll();
        $o_inventory->clearAll();

        $inventory->setContents(
            [
                0 => new Teleport(),
                1 => new PlayerInfo(),
                2 => new Freeze(),
                3 => new InventorySee(),
                4 => new EnderInventorySee(),
                5 => new Punishment(),
                8 => new Vanish()
            ]
        );

        $this->player->setAllowFlight(true);
    }

    public function close(): void
    {
        $inventory = $this->player->getInventory();
        $a_inventory = $this->player->getInventory();
        $o_inventory = $this->player->getInventory();

        $inventory->clearAll();
        $a_inventory->clearAll();
        $o_inventory->clearAll();

        $inventory->setContents($this->items);
        $a_inventory->setContents($this->armor);
        $o_inventory->setContents($this->offhand);

        $this->player->setAllowFlight(false);
        $this->player->setFlying(false);

        ClydeUtils::showToPlayers($this->player);

        $this->player->teleport($this->first);

        if ($this->isVanished()) {
            $this->player->getEffects()->clear();
        }
    }

    public function isVanished(): bool
    {
        return $this->vanish;
    }

    public function setVanish(bool $vanish): void
    {
        $this->vanish = $vanish;

        if ($vanish) {
            Loader::getInstance()->getScheduler()->scheduleRepeatingTask(
                new VanishScheduler($this), 20
            );
        }
    }

    public function setStaffchat(bool $staffchat): void
    {
        $this->staffchat = $staffchat;
    }

    public function isStaffChat(): bool
    {
        return $this->staffchat;
    }

    public function showOtherStaff(): void
    {
        $sessions = SessionFactory::getInstance()->getSessions();
        foreach ($sessions as $session) {

            if ($session->isVanished()) {
                continue;
            }

            $player = $session->getPlayer();
            if (!$player->isOnline()) {
                continue;
            }

            $this->player->showPlayer($player);
        }
    }

    public function hideOtherStaff(): void
    {
        $sessions = SessionFactory::getInstance()->getSessions();
        foreach ($sessions as $session) {

            if (!$session->isVanished()) {
                continue;
            }

            $player = $session->getPlayer();
            if (!$player->isOnline()) {
                continue;
            }

            $this->player->hidePlayer($player);
        }
    }
}