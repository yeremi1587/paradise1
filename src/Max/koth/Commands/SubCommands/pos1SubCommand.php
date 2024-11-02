<?php

declare(strict_types=1);

namespace Max\koth\Commands\SubCommands;

use CortexPE\Commando\args\StringArgument;
use CortexPE\Commando\BaseSubCommand;
use Max\koth\KOTH;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;

class pos1SubCommand extends BaseSubCommand {
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
            $sender->sendMessage("§c(§8RaveKOTH§c) §7No existe una arena con ese nombre");
            return;
        }

        $position = $sender->getPosition();
        $arena->setMin($position);
        
        $sender->sendMessage("§c(§8RaveKOTH§c) §7Posición 1 establecida correctamente");
    }

    protected function prepare(): void {
        $this->setPermission("maxkoth.command.pos1");
        $this->registerArgument(0, new StringArgument("arena", false));
    }
}
