
<?php

declare(strict_types=1);

namespace Max\koth\Commands\SubCommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use Max\koth\KOTH;
use pocketmine\command\CommandSender;

class startSubCommand extends BaseSubCommand {
	public function prepare(): void {
		$this->setPermission("maxkoth.command.koth.start");
		$this->registerArgument(0, new RawStringArgument("arena", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		$koth = KOTH::getInstance();
		
		// Check if arena name is provided
		if (!isset($args["arena"])) {
			$sender->sendMessage("§cUso: /koth start <nombre_arena>");
			$sender->sendMessage("§7Usa /koth list para ver las arenas disponibles");
			return;
		}
		
		$arena = $koth->getArena($args["arena"]);
		if (!$arena) {
			$sender->sendMessage("§fKOTH » Esta arena no existe.");
			return;
		}
		
		// Make sure arena has a spawn point set
		if ($arena->getSpawn() === null) {
			$sender->sendMessage("§fKOTH » Esta arena no tiene punto de aparición. Usa /koth setspawn primero.");
			return;
		}
		
		$sender->sendMessage($koth->startKoth($arena));
	}
}
