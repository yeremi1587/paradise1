
<?php

namespace xoapp\clyde\commands\other;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use xoapp\clyde\player\PlayerData;
use xoapp\clyde\utils\ClydeUtils;
use xoapp\clyde\data\PermanentlyData;
use xoapp\clyde\data\TemporarilyData;

class AltsCommand extends Command
{
    public function __construct()
    {
        parent::__construct("alts");
        $this->setPermission("alts.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermissionSilent($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§cUso: /alts <jugador>");
            return;
        }

        $i_player = ClydeUtils::getPlayerByPrefix($args[0]);
        $playerName = $i_player ? $i_player->getName() : $args[0];
        
        $alts = PlayerData::getInstance()->getPossibleAlts($playerName);
        if (sizeof($alts) <= 0) {
            $sender->sendMessage("§c" . $playerName . " no tiene cuentas alternativas.");
            return;
        }

        $sender->sendMessage("§6• §f" . $playerName . " §7posibles cuentas: §f" . implode(", ", $alts));
        $sender->sendMessage("§6Infracciones:");

        // Verificar infracciones del jugador principal
        $this->checkInfractions($sender, $playerName);
        
        // Verificar infracciones de las cuentas alternativas
        foreach ($alts as $alt) {
            $this->checkInfractions($sender, $alt);
        }
    }

    private function checkInfractions(CommandSender $sender, string $player): void 
    {
        $permanentBans = PermanentlyData::getInstance()->getData($player);
        $temporaryBans = TemporarilyData::getInstance()->getData($player);

        if ($permanentBans !== false) {
            $sender->sendMessage("§c• " . $player . ": Ban permanente - " . $permanentBans["reason"]);
        }
        
        if ($temporaryBans !== false) {
            $sender->sendMessage("§e• " . $player . ": Ban temporal - " . $temporaryBans["reason"]);
        }
    }
}
