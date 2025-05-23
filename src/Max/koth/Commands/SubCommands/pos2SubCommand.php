<?php

declare(strict_types=1);

namespace Max\koth\Commands\SubCommands;

use CortexPE\Commando\args\StringArgument;
use CortexPE\Commando\BaseSubCommand;
use Max\koth\KOTH;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;

class pos2SubCommand extends BaseSubCommand {
    private KOTH $plugin;

    public function __construct(string $name, string $description = "", array $aliases = []) {
        $this->plugin = KOTH::getInstance();
        parent::__construct($name, $description, $aliases);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        $arena = $this->plugin->getArena($args["arena"]);
        if ($arena === null) {
            $sender->sendMessage("KOTH » §7No existe una arena con ese nombre");
            return;
        }

        $position = $sender->getPosition();
        $arena->setMax($position);
        
        $sender->sendMessage("KOTH » §7Posición 2 establecida correctamente");
    }

    protected function prepare(): void {
        $this->setPermission("maxkoth.command.pos2");
        $this->registerArgument(0, new StringArgument("arena", false));
    }
}