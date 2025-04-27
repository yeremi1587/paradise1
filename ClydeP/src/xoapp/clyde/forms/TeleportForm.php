<?php

namespace xoapp\clyde\forms;

use pocketmine\player\Player;
use xoapp\clyde\library\forms\SimpleForm;
use xoapp\clyde\utils\ClydeUtils;
use xoapp\clyde\utils\Prefixes;

class TeleportForm extends SimpleForm
{

    public function __construct(Player $player)
    {
        parent::__construct(
            function (Player $player, string $data = null): void {
                if (is_null($data)) {
                    return;
                }

                $i_player = ClydeUtils::getPlayerExact($data);
                if (!$i_player instanceof Player) {
                    $player->sendMessage(Prefixes::GLOBAL . "This player is not online!");
                    return;
                }

                $player->teleport($i_player->getPosition());
                $player->sendMessage(
                    Prefixes::GLOBAL . "You have teleported to §a" . $i_player->getName()
                );
            }
        );

        $this->setTitle("Online Players");

        foreach (ClydeUtils::getPlayers() as $players) {

            if (hash_equals($player->getName(), $players->getName())) {
                continue;
            }

            $this->setButton(
                $players->getName(), 0, "textures/ui/icon_steve", $players->getName()
            );
        }
    }
}