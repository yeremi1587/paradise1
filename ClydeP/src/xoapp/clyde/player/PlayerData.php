<?php

namespace xoapp\clyde\player;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use xoapp\clyde\Loader;

class PlayerData
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
            Loader::getInstance()->getDataFolder() . "players.json",
            Config::JSON
        );
    }

    public function register(Player $player): void
    {
        $data = [
            "xuid" => $player->getXuid(),
            "address" => $player->getNetworkSession()->getIp(),
            "first_login" => date("Y-m-d H:i:s"),
            "country" => "",
            "city" => "",
            "region" => ""
        ];

        $this->data->set($player->getName(), $data);
        $this->data->save();
    }

    public function unregister(Player $player): void
    {
        $this->data->remove($player->getName());
        $this->data->save();
    }

    public function getData(Player|string $player): array|bool
    {
        return $this->data->get(is_string($player) ? $player : $player->getName());
    }

    public function exists(Player $player): bool
    {
        return $this->data->exists($player->getName());
    }

    public function editData(Player $player, array $values): void
    {
        $data = $this->getData($player);

        foreach ($values as $key => $value) {
            $data[$key] = $value;
        }

        $this->data->set($player->getName(), $data);
        $this->data->save();
    }

    public function getSavedData(): array
    {
        return $this->data->getAll();
    }

    public function getPossibleAlts(string $name): array
    {
        $accounts = $this->getSavedData();
        $address = $accounts[$name]["address"];
        $alts = [];

        foreach ($accounts as $account => $data) {

            $a_address = $data["address"];
            if ($account === $name) {
                continue;
            }

            if ($address !== $a_address) {
                continue;
            }

            $alts[] = $account;
        }

        return $alts;
    }
}