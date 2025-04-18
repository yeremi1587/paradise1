<?php

/**
 *
 *  _     _  _______  ______    ___      ______   _______  __   __  _______  ______    ______
 * | | _ | ||       ||    _ |  |   |    |      | |       ||  | |  ||   _   ||    _ |  |      |
 * | || || ||   _   ||   | ||  |   |    |  _    ||    ___||  | |  ||  |_|  ||   | ||  |  _    |
 * |       ||  | |  ||   |_||_ |   |    | | |   ||   | __ |  |_|  ||       ||   |_||_ | | |   |
 * |       ||  |_|  ||    __  ||   |___ | |_|   ||   ||  ||       ||       ||    __  || |_|   |
 * |   _   ||       ||   |  | ||       ||       ||   |_| ||       ||   _   ||   |  | ||       |
 * |__| |__||_______||___|  |_||_______||______| |_______||_______||__| |__||___|  |_||______|
 *
 * By MihaiChirculete.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GitHub: https://github.com/MihaiChirculete
 */

namespace MihaiChirculete\WorldGuard;

use MihaiChirculete\WorldGuard\ResourceUtils\ResourceManager;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\entity\effect\Effect;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\utils\TextFormat as TF;

class Region
{

    private $pos1 = [];
    private $pos2 = [];
    private $levelname = "";
    private $name = "";
    private $flags = [];

    private $level;
    private $effects = [];

    public function __construct(string $name, array $pos1, array $pos2, string $level, array $flags)
    {
        $this->name = $name;
        $this->pos1 = $pos1;
        $this->pos2 = $pos2;
        $this->levelname = $level;

        foreach (WorldGuard::FLAGS as $k => $v) {
            if (!isset($flags[$k])) $flags[$k] = $v;
        }
        $this->flags = $flags;

        $this->effects = [];
        
        if (isset($this->flags["effects"]) && is_array($this->flags["effects"])) {
            foreach ($this->flags["effects"] as $id => $amplifier) {
                $effectType = EffectIdMap::getInstance()->fromId($id);
                if ($effectType !== null) {
                    $this->effects[$id] = new EffectInstance($effectType, 999999999, $amplifier, false);
                }
            }
        }
        
        $this->level = Server::getInstance()->getWorldManager()->getWorldByName($level);
    }

    public function getPos1(): array
    {
        return $this->pos1;
    }

    public function getPos2(): array
    {
        return $this->pos2;
    }

    public function getLevelName(): string
    {
        return $this->levelname;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLevel(): World
    {
        return $this->level;
    }

    public function getFlags(): array
    {
        return $this->flags;
    }

    public function getFlag(string $flag)
    {
        return $this->flags[$flag];
    }

    public function setFlag(string $flag, array $avalue)
    {
        $value = $avalue[0];

        if ($flag === "effects") {
            if (!is_numeric($value)) {
                return TF::RED . "Value of effect flag must be numeric.";
            }
            
            if (intval($value) <= 0) {
                $this->flags["effects"] = [];
                $this->effects = [];
                ResourceManager::getInstance()->saveRegions(ResourceManager::getInstance()->pluginInstance->getRegions());
                return TF::YELLOW . 'All "effects" (of "' . $this->name . '") have been removed.';
            }
            
            if (isset($avalue[1])) {
                if (is_numeric($avalue[1])) {
                    $effectId = intval($value);
                    $amplifier = intval($avalue[1]);
                    
                    $this->flags["effects"][$effectId] = $amplifier;
                    
                    $effectType = EffectIdMap::getInstance()->fromId($effectId);
                    if ($effectType !== null) {
                        $this->effects[$effectId] = new EffectInstance($effectType, 999999999, $amplifier, false);
                        
                        ResourceManager::getInstance()->saveRegions(ResourceManager::getInstance()->pluginInstance->getRegions());
                        return TF::YELLOW . 'Added "' . $effectType->getName() . ' ' . Utils::getRomanNumber(++$amplifier) . '" effect to "' . $this->name . '" region.';
                    } else {
                        return TF::RED . "Invalid effect ID: " . $effectId;
                    }
                } else {
                    return TF::RED . "Amplifier must be numerical.\n" . TF::GRAY . 'Example: /region flags set ' . $this->name . ' effects ' . $value . ' 1';
                }
            } else {
                $effectId = intval($value);
                $this->flags["effects"][$effectId] = 0;
                
                $effectType = EffectIdMap::getInstance()->fromId($effectId);
                if ($effectType !== null) {
                    $this->effects[$effectId] = new EffectInstance($effectType, 999999999, 0, false);
                    
                    ResourceManager::getInstance()->saveRegions(ResourceManager::getInstance()->pluginInstance->getRegions());
                    return TF::YELLOW . 'Added "' . $effectType->getName() . '" effect to "' . $this->name . '" region.';
                } else {
                    return TF::RED . "Invalid effect ID: " . $effectId;
                }
            }
        }

        switch (WorldGuard::FLAG_TYPE[$flag]) {
            case "integer":
                if ($flag === "fly-mode") {
                    if ($value < 0 || $value > 3) {
                        return implode("\n", [
                            TF::RED . 'Flight flag should be either 0, 1, 2 or 3.',
                            TF::GRAY . '0 => No changes to flight. Vanilla behaviour.',
                            TF::GRAY . '1 => Enable flight.',
                            TF::GRAY . '2 => Disable flight.',
                            TF::GRAY . '3 => Enable flight, but disable it when the player leaves the area.'
                        ]);
                    }
                    $this->flags["fly-mode"] = (int)$value;
                    return TF::YELLOW . '"Flight mode" (of "' . $this->name . '") was changed to: ' . $value . '.';
                }
                break;
            case "boolean":
                if ($value !== "true" && $value !== "false") {
                    return TF::RED . 'Value of "' . $flag . '" must either be "true" or "false"';
                }
                break;
            case "array":
                if (!is_string($value)) {
                    return TF::RED . 'Value of ' . $flag . ' must be a string.';
                }
                $this->flags[$flag][$value] = "";
                if ($flag === "game-mode") {
                    if($value === "ignore"){
                        $this->flags["game-mode"] = "ignore";
                        return TF::YELLOW . $this->name . "'s gamemode has been set to Ignore";
                    }
                    $gm = Utils::GAMEMODES[$value] ?? 0;
                    $this->flags["game-mode"] = $gm;
                    return TF::YELLOW . $this->name . "'s gamemode has been set to " . Utils::gm2string($gm) . '.';
                }
                return;
        }

        if ($flag === "notify-enter" || $flag === "notify-leave") {
            $msg = implode(" ", str_replace("&", "§", $avalue));
            $this->flags[$flag] = $msg;
        }else {
            if ($flag === "console-cmd-on-enter" || $flag === "console-cmd-on-leave") {
                $this->flags[$flag] = implode(" ", $avalue);
            } else {
                $this->flags[$flag] = str_replace("&", "§", $value);
            }
        }
        return TF::YELLOW . 'Flag "' . $flag . '" (of "' . $this->name . '") has been updated to "' . $this->flags[$flag] . '".';
    }

    public function resetFlag(string $flag)
    {
        $this->flags[$flag] = WorldGuard::FLAGS[$flag];
    }

    public function getBlockedCmds(): string
    {
        $blocked = $this->flags["blocked-cmds"];
        return empty($blocked) ? "none" : "[" . implode(", ", array_keys($blocked)) . "]";
    }

    public function getAllowedCmds(): string
    {
        $allowed = $this->flags["allowed-cmds"];
        return empty($allowed) ? "none" : "[" . implode(", ", array_keys($allowed)) . "]";
    }

    public function getEffectsString()
    {
        return empty($effects = $this->flags["effects"]) ? "none" : implode(", ", $effects);
    }

    public function getFlagsString(): string
    {
        $array = [];
        foreach ($this->flags as $flag => $value) {
            switch ($flag) {
                case "blocked-cmds":
                    $array[] = $flag . ' => ' . TF::GRAY . $this->getBlockedCmds();
                    break;
                case "allowed-cmds":
                    $array[] = $flag . ' => ' . TF::GRAY . $this->getAllowedCmds();
                    break;
                case "effects":
                    $array[] = $flag . ' => ' . TF::GRAY . $this->getEffectsString();
                    break;
                default:
                    $array[] = $flag . ' => ' . TF::GRAY . '"' . $value . '"';
                    break;
            }
        }
        return TF::LIGHT_PURPLE . implode(TF::WHITE . ', ' . TF::LIGHT_PURPLE, $array);
    }

    public function isCommandAllowed(string $command): bool
    {
        if (empty($allowed = $this->flags["allowed-cmds"])) {
            if (!empty($blocked = $this->flags["blocked-cmds"])) {
                return !isset($blocked[$command]);
            }
            return true;
        }
        return isset($allowed[$command]);
    }

    public function getEffects(): array
    {
        return $this->effects;
    }

    public function getGamemode(): string
    {
        return $this->flags["game-mode"];
    }

    public function getFlight(): int
    {
        return $this->flags["fly-mode"];
    }

    public function toArray(): array
    {
        return ["pos1" => $this->pos1, "pos2" => $this->pos2, "level" => $this->levelname, "flags" => $this->flags];
    }
}
