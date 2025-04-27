<?php

namespace xoapp\clyde\profile\factory;

use pocketmine\player\Player;
use xoapp\clyde\profile\Profile;

class ProfileFactory
{

    /** @var Profile[] */
    private static array $profiles = [];

    public static function register(Player $player): void
    {
        self::$profiles[$player->getName()] = new Profile($player->getName());
    }

    public static function getProfile(Player $player): ?Profile
    {
        return self::$profiles[$player->getName()] ?? null;
    }

    public static function unregister(Player $player): void
    {
        unset(self::$profiles[$player->getName()]);
    }

    public static function saveAll(): void
    {
        foreach (self::$profiles as $profile) {
            $profile->save();
        }
    }
}