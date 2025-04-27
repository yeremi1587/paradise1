
<?php

namespace xoapp\clyde\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use xoapp\clyde\session\SessionFactory;
use xoapp\clyde\utils\Prefixes;
use xoapp\clyde\utils\ClydeUtils;

class ModCommand extends Command
{
    public function __construct()
    {
        parent::__construct("clyde");
        $this->setPermission("staffmode.command");
        $this->setAliases(["staff", "mod"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        $session = SessionFactory::getInstance()->get($sender);
        if (is_null($session)) {
            SessionFactory::getInstance()->register($sender);
            $sender->sendMessage(Prefixes::GLOBAL . "¡Ahora estás en modo StaffMode!");
            
            // Solo enviar mensaje de desconexión y ocultar de la lista
            ClydeUtils::globalMessage("§e" . $sender->getName() . " §cse ha desconectado");
            ClydeUtils::hideToPlayers($sender);
            
            return;
        }

        $session->close();
        SessionFactory::getInstance()->unregister($sender);
        ClydeUtils::showToPlayers($sender);
        $sender->sendMessage(Prefixes::GLOBAL . "¡Ya no estás en modo StaffMode!");
    }
}
