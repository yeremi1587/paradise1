<?php

namespace xoapp\clyde\data;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use xoapp\clyde\Loader;

class MuteData
{
    use SingletonTrait {
        setInstance as private;
        reset as private;
    }

    private Config $data;

    public function __construct()
    {
        self::setInstance($this);

        $this->data = new Config(
            Loader::getInstance()->getDataFolder() . "muted.json",
            Config::JSON
        );
    }

    public function setData(string $key, array $data): void
    {
        $this->data->set($key, $data);
        $this->data->save();
    }

    public function getData(string $key): array
    {
        return $this->data->get($key);
    }

    public function exists(string $key): bool
    {
        return $this->data->exists($key);
    }

    public function removeData(string $key): void
    {
        $this->data->remove($key);
        $this->data->save();
    }

    public function getSavedData(): array
    {
        return $this->data->getAll(true);
    }
}