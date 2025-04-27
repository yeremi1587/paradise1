<?php

namespace xoapp\clyde\profile\data;

use xoapp\clyde\Loader;
use pocketmine\utils\Config;

class ProfileData
{

    private static Config $config;

    public static function load(): void
    {
        self::$config = new Config(
            Loader::getInstance()->getDataFolder() . "profiles.json", Config::JSON
        );
    }

    public static function setData(string $key, array $data): void
    {
        self::$config->set($key, $data);
        self::$config->save();
    }

    public static function getData(string $key): ?array
    {
        return self::$config->get($key, null);
    }

    public static function exists(string $key): bool
    {
        return self::$config->exists($key);
    }

    public static function getSavedData(): array
    {
        return self::$config->getAll();
    }

    public static function deleteIdData(string $key, int $id): void
    {
        $data = self::$config->get($key);

        foreach ($data as $i => $value) {
            if ($i === $id) {
                unset($data[$i]);
            }
        }

        self::setData($key, $data);
    }
}