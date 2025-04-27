
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
            
            // Hacer que el jugador sea invisible para todos y enviar mensaje de desconexión
            ClydeUtils::globalMessage("§e" . $sender->getName() . " §cse ha desconectado");
            ClydeUtils::hideToPlayers($sender);
            
            // Marcar al jugador como escondido en la lista de jugadores
            $sender->setInvisible(true);
            
            // Mostrar el jugador a otros miembros del staff que también están en modo staff
            $this->showToOtherStaff($sender);
            
            return;
        }

        $session->close();
        SessionFactory::getInstance()->unregister($sender);
        
        // Hacer visible al jugador nuevamente
        $sender->setInvisible(false);
        ClydeUtils::showToPlayers($sender);
        $sender->sendMessage(Prefixes::GLOBAL . "¡Ya no estás en modo StaffMode!");
    }
    
    /**
     * Hace visible al jugador sólo para otros miembros del staff que están en modo staff
     */
    private function showToOtherStaff(Player $player): void
    {
        $sessions = SessionFactory::getInstance()->getSessions();
        foreach ($sessions as $session) {
            if ($session->getPlayer() !== null && $session->getPlayer()->isOnline()) {
                // El jugador en staffmode puede ver a otros en staffmode
                $session->getPlayer()->showPlayer($player);
                // Y el jugador actual puede ver a otros en staffmode
                $player->showPlayer($session->getPlayer());
            }
        }
    }
}
