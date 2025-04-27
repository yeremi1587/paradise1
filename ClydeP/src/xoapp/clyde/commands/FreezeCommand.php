<?php

namespace xoapp\clyde\commands;

use pocketmine\player\Player;
use pocketmine\command\Command;
use xoapp\clyde\utils\Prefixes;
use xoapp\clyde\utils\ClydeUtils;
use pocketmine\command\CommandSender;
use xoapp\clyde\session\SessionFactory;

class FreezeCommand extends Command
{

    public function __construct()
    {
        parent::__construct("freeze");

        $this->setPermission("freeze.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage(Prefixes::GLOBAL . "Use /freeze (player)");
            return;
        }

        $i_player = ClydeUtils::getPlayerByPrefix($args[0]);
        if (!$i_player instanceof Player) {
            $sender->sendMessage(Prefixes::GLOBAL . "This player is not online");
            return;
        }

        $f_session = SessionFactory::getInstance()->getFreeze($i_player);
        if (is_null($f_session)) {
            SessionFactory::getInstance()->registerFreeze($i_player);

            ClydeUtils::globalMessage(
                Prefixes::GLOBAL . "The player §e" . $i_player->getName() . " §7has been frozen by §a" . $sender->getName()
            );
            return;
        }

        $f_session->close();
        SessionFactory::getInstance()->unregisterFreeze($i_player);

        ClydeUtils::globalMessage(
            Prefixes::GLOBAL . "The player §e" . $i_player->getName() . " §7has been unfrozen by §a" . $sender->getName()
        );
    }
}