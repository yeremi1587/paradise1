<?php

namespace xoapp\clyde\commands;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use xoapp\clyde\session\SessionFactory;

class StaffChatCommand extends Command
{

    public function __construct()
    {
        parent::__construct("sc");
        $this->setAliases(["staffchat"]);
        $this->setPermission("staffmode.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {

        if (!$sender instanceof Player) {
            return;
        }

        $session = SessionFactory::getInstance()->get($sender);
        if (is_null($session)) {
            $sender->sendMessage(TextFormat::colorize("&cYou need to enter in staffmode first"));
            return;
        }

        if ($session->isStaffChat()) {
            $session->setStaffchat(false);
            $sender->sendMessage(TextFormat::colorize("&aYou successfully exited the staffmode"));
            return;
        }

        $session->setStaffchat(true);
        $sender->sendMessage(TextFormat::colorize("&aYou successfully entered the staffmode"));
    }
}