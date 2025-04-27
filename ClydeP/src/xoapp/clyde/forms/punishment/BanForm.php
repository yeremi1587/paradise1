<?php

namespace xoapp\clyde\forms\punishment;

use pocketmine\player\Player;
use xoapp\clyde\utils\Prefixes;
use xoapp\clyde\utils\ClydeUtils;
use xoapp\clyde\data\TemporarilyData;
use xoapp\clyde\formatter\TimeFormatter;
use xoapp\clyde\library\forms\CustomForm;

class BanForm extends CustomForm
{

    public function __construct(Player $i_player)
    {
        parent::__construct(
            function (Player $player, ?array $data = null) use ($i_player): void {

                if (is_null($data)) {
                    return;
                }

                if (
                    $data["time"] == null ||
                    $data["reason"] == null
                ) {
                    $player->sendMessage(Prefixes::GLOBAL . "Fill missing data");
                    return;
                }

                if (!$i_player->isOnline()) {
                    return;
                }

                if (!TimeFormatter::isValidFormat($data["time"])) {
                    $player->sendMessage(Prefixes::GLOBAL . "Please set a valid time format");
                    return;
                }

                TemporarilyData::getInstance()->setData($i_player->getName(), [
                    "duration" => TimeFormatter::parseTime($data["time"]),
                    "reason" => $data["reason"],
                    "date" => date("Y-m-d H:i:s"),
                    "sender" => $player->getName()
                ]);

                ClydeUtils::globalMessage(
                    Prefixes::GLOBAL . "§e" . $i_player->getName() . "§7 has been banned for §c" . $data["reason"]
                );

                $i_player->kick("§cYou have been banned by §f" . $player->getName());

                $player->sendMessage(Prefixes::GLOBAL . "You successfully banned " . $i_player->getName());
            }
        );

        $this->setTitle("Ban " . $i_player->getName());

        $this->setInput(
            "Reason", "Example: Hacks", null, "reason"
        );

        $this->setInput(
            "Time", "Example: 24d", null, "time"
        );
    }
}