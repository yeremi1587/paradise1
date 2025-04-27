<?php

namespace xoapp\clyde\scheduler\async;

use pocketmine\Server;
use xoapp\clyde\player\PlayerData;
use pocketmine\scheduler\AsyncTask;

class GetCountryAsync extends AsyncTask
{

    private string $address;

    private string $player;

    public function __construct(string $player, string $address)
    {
        $this->address = $address;
        $this->player = $player;
    }

    public function onRun(): void
    {
        $address = $this->address;

        $http = file_get_contents('http://www.geoplugin.net/json.gp?ip=' . $address);

        $handle = json_decode($http);

        $country = is_null($handle->geoplugin_countryName) ?
            "Unknown" :
            $handle->geoplugin_countryName;

        $city = is_null($handle->geoplugin_city) ?
            "Unknown" :
            $handle->geoplugin_city;

        $region = is_null($handle->geoplugin_regionName) ?
            "Unknown" :
            $handle->geoplugin_regionName;

        $this->setResult([
            "country" => $country,
            "city" => $city,
            "region" => $region,
        ]);
    }

    public function onCompletion(): void
    {
        $result = $this->getResult();

        $player = Server::getInstance()->getPlayerExact(
            $this->player
        );

        PlayerData::getInstance()->editData($player, [
            "country" => $result["country"],
            "city" => $result["city"],
            "region" => $result["region"],
        ]);
    }
}