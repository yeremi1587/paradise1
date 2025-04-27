<?php

namespace xoapp\clyde\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use xoapp\clyde\Loader;

class MuteChatCommand extends Command
{

    public function __construct()
    {
        parent::__construct("mutechat", "Mute Global chat");
        $this->setPermission("mutechat.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (Loader::getInstance()->isMutedChat()) {
            Loader::getInstance()->setMutedChat(false);
            $sender->sendMessage(TextFormat::colorize("&aGlobal chat un muted"));
            return;
        }

        Loader::getInstance()->setMutedChat(true);
        $sender->sendMessage(TextFormat::colorize("&aGlobal chat muted"));
    }
}