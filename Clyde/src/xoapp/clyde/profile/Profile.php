<?php

namespace xoapp\clyde\profile;

use pocketmine\Server;
use xoapp\clyde\Loader;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use xoapp\clyde\profile\data\ProfileData;
use xoapp\clyde\library\serializer\Serializer;

class Profile
{

    private array $warns = [];
    private array $saved_data = [];

    public function __construct(
        private readonly string $name
    )
    {
        $this->load();
    }

    public function load(): void
    {
        $saved_data = ProfileData::getData($this->name);
        foreach ($saved_data as $data) {

            $this->warns = $data["warns"] ?? [];

            $rollbacks = $data["rollbacks"] ?? [];
            foreach ($rollbacks as $rollback) {
                $rollback["contents"] = Serializer::deserialize($rollback["contents"]);
                $rollback["armor"] = Serializer::deserialize($rollback["armor"]);
                $rollback["offhand"] = Serializer::deserialize($rollback["offhand"]);

                $this->addLog($rollback);
            }
        }

        Loader::getInstance()->getLogger()->info(
            TextFormat::GREEN . $this->name . " Loaded " . sizeof($this->saved_data) . " Rollback data"
        );
        Loader::getInstance()->getLogger()->info(
            TextFormat::GREEN . $this->name . " Loaded " . sizeof($this->saved_data) . " Warn data"
        );

        $this->save();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPlayer(): ?Player
    {
        return Server::getInstance()->getPlayerExact($this->name);
    }

    public function addLog(array $data): void
    {
        if (sizeof($this->saved_data) >= 53) {
            $this->saved_data[53] = $data;
        } else {
            $this->saved_data[sizeof($this->saved_data) + 1] = $data;
        }
    }

    public function getLog(int $id): ?array
    {
        return $this->saved_data[$id] ?? null;
    }

    public function deleteLog(int $id): void
    {
        unset($this->saved_data[$id]);

        ProfileData::deleteIdData($this->name, $id);
    }

    public function addWarn(array $data): void
    {
        $this->warns[sizeof($this->warns) + 1] = $data;
    }

    public function getWarns(): array
    {
        return $this->warns;
    }

    public function getWarn(int $id): ?array
    {
        return $this->warns[$id] ?? null;
    }

    public function getSavedData(): array
    {
        return $this->saved_data;
    }

    public function giveData(int $id): void
    {
        $player = $this->getPlayer();
        if (is_null($player)) {
            return;
        }

        $data = $this->getLog($id);

        $player->getInventory()->setContents($data["contents"]);
        $player->getArmorInventory()->setContents($data["armor"]);
        $player->getOffHandInventory()->setContents($data["offhand"]);

        $player->sendMessage(
            TextFormat::colorize("&aYou got a rollback #" . $id)
        );

        $this->deleteLog($id);
    }

    public function jsonSerialize(): array
    {
        $result = [
            "warns" => [],
            "rollbacks" => []
        ];

        foreach ($this->warns as $key => $data) {
            $result["warns"][$key] = $data;
        }

        foreach ($this->saved_data as $key => $data) {
            $data["contents"] = Serializer::serialize($data["contents"]);
            $data["armor"] = Serializer::serialize($data["armor"]);
            $data["offhand"] = Serializer::serialize($data["offhand"]);

            $result["rollbacks"][$key] = $data;
        }

        return $result;
    }

    public function save(): void
    {
        ProfileData::setData(
            $this->name, $this->jsonSerialize()
        );
    }
}