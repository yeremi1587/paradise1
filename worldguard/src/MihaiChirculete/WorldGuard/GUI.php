
<?php

namespace MihaiChirculete\WorldGuard;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use MihaiChirculete\WorldGuard\forms\{CustomForm, CustomFormResponse, MenuForm};
use MihaiChirculete\WorldGuard\elements\{Button, Dropdown, Image, Input, Label, Toggle};

class GUI
{
    public static $currentlyEditedRg = "";

    public static function displayMenu(Player $issuer)
    {
        $plugin = Utils::getPluginFromIssuer($issuer);
        $lang = $plugin->resourceManager->getLanguagePack();

        // Create buttons explicitly with proper constructors
        $managementButton = new Button("§6§l" . $lang["gui_btn_rg_management"]);
        $managementButton->setValue(0);
        $helpButton = new Button("§5§l" . $lang["gui_btn_help"]);
        $helpButton->setValue(1);
        $buttons = [$managementButton, $helpButton];

        // Create the menu form
        $menuForm = new MenuForm(
            "§9§l" . $lang["gui_wg_menu_title"],
            $lang["gui_label_choose_option"],
            $buttons,
            function (Player $player, Button $selected): void {
                switch ($selected->getValue()) {
                    case 0:
                        self::displayRgManagement($player);
                        break;
                    case 1:
                        self::displayHelpMenu($player);
                        break;
                }
            }
        );
        
        $issuer->sendForm($menuForm);
    }

    public static function displayRgManagement(Player $issuer)
    {
        $lang = Utils::getPluginFromIssuer($issuer)->resourceManager->getLanguagePack();

        // Create buttons explicitly
        $manageExistingButton = new Button($lang["gui_btn_manage_existing"]);
        $manageExistingButton->setValue(0);
        $createRegionButton = new Button($lang["gui_btn_create_region"]);
        $createRegionButton->setValue(1);
        $redefineRegionButton = new Button($lang["gui_btn_redefine_region"]);
        $redefineRegionButton->setValue(2);
        $deleteRegionButton = new Button($lang["gui_btn_delete_region"]);
        $deleteRegionButton->setValue(3);
        
        $buttons = [
            $manageExistingButton,
            $createRegionButton,
            $redefineRegionButton,
            $deleteRegionButton
        ];

        $issuer->sendForm(new MenuForm(
            "§9§l" . $lang["gui_btn_rg_management"], 
            $lang["gui_label_choose_option"],
            $buttons,
            function (Player $player, Button $selected): void {
                switch ($selected->getValue()) {
                    case 0:
                        self::displayExistingRegions($player);
                        break;
                    case 1:
                        self::displayRgCreation($player);
                        break;
                    case 2:
                        self::displayRgRedefine($player);
                        break;
                    case 3:
                        self::displayRgDelete($player);
                        break;
                }
            }
        ));
    }

    public static function displayRgCreation(Player $issuer)
    {
        $lang = Utils::getPluginFromIssuer($issuer)->resourceManager->getLanguagePack();

        $issuer->sendForm(new CustomForm("§9§l" . $lang["gui_creation_menu_title"],
            [
                new Label($lang["gui_creation_menu_label1"]),
                new Input($lang["gui_creation_menu_rg_name_box"], "MyRegion"),
                new Label($lang["gui_creation_menu_label2"]),
                new Toggle($lang["gui_creation_menu_toggle_expand"], false),
                new Label($lang["gui_creation_menu_label3"])
            ],
            function (Player $player, CustomFormResponse $response): void {
                try {
                    $values = $response->getValues();
                    if (count($values) >= 2) {
                        $rgName = $values[0] ?? "MyRegion";
                        $extended = $values[1] ?? false;
                        
                        $rgName = is_string($rgName) ? $rgName : (string)$rgName;
                        $extended = is_bool($extended) ? $extended : ($extended === "true" || $extended === "1");
                        
                        if ($extended === true) {
                            $player->getServer()->dispatchCommand($player, "rg create $rgName extended");
                        } else {
                            $player->getServer()->dispatchCommand($player, "rg create $rgName");
                        }
                    } else {
                        $player->sendMessage("§c" . "Error al procesar el formulario: faltan datos.");
                    }
                } catch (\Exception $e) {
                    $player->getServer()->getLogger()->error("Error in region creation: " . $e->getMessage());
                    $player->sendMessage("§c" . "Error al crear la región. Por favor, inténtalo de nuevo.");
                }
            }
        ));
    }

    public static function displayRgRedefine(Player $issuer)
    {
        $lang = Utils::getPluginFromIssuer($issuer)->resourceManager->getLanguagePack();
        $plugin = Utils::getPluginFromIssuer($issuer);
        
        $regions = array_keys($plugin->getRegions());
        if (empty($regions)) {
            $issuer->sendMessage("§c" . ($lang["gui_no_regions_error"] ?? "No hay regiones creadas aún. Crea una región primero."));
            return;
        }

        $issuer->sendForm(new CustomForm("§9§l" . $lang["gui_btn_rg_management"],
            [
                new Dropdown($lang["gui_dropdown_select_redefine"], $regions),
            ],
            function (Player $player, CustomFormResponse $response) use ($plugin): void {
                try {
                    $values = $response->getValues();
                    $rgIndex = $values[0] ?? 0;
                    
                    $regions = array_keys($plugin->getRegions());
                    if (isset($regions[$rgIndex])) {
                        $rgName = $regions[$rgIndex];
                        $player->getServer()->dispatchCommand($player, "rg redefine $rgName");
                    } else {
                        $player->sendMessage("§c" . "Error: La región seleccionada no existe.");
                    }
                } catch (\Exception $e) {
                    $player->getServer()->getLogger()->error("Error in region redefine: " . $e->getMessage());
                    $player->sendMessage("§c" . "Error al redefinir la región. Por favor, inténtalo de nuevo.");
                }
            }
        ));
    }

    public static function displayRgDelete(Player $issuer)
    {
        $lang = Utils::getPluginFromIssuer($issuer)->resourceManager->getLanguagePack();
        $plugin = Utils::getPluginFromIssuer($issuer);
        
        $regions = array_keys($plugin->getRegions());
        if (empty($regions)) {
            $issuer->sendMessage("§c" . ($lang["gui_no_regions_error"] ?? "No hay regiones creadas aún. Crea una región primero."));
            return;
        }

        $issuer->sendForm(new CustomForm("§9§l" . $lang["gui_btn_rg_management"],
            [
                new Dropdown($lang["gui_dropdown_select_delete"], $regions),
            ],
            function (Player $player, CustomFormResponse $response) use ($plugin): void {
                try {
                    $values = $response->getValues();
                    $rgIndex = $values[0] ?? 0;
                    
                    $regions = array_keys($plugin->getRegions());
                    if (isset($regions[$rgIndex])) {
                        $rgName = $regions[$rgIndex];
                        $player->getServer()->dispatchCommand($player, "rg delete $rgName");
                    } else {
                        $player->sendMessage("§c" . "Error: La región seleccionada no existe.");
                    }
                } catch (\Exception $e) {
                    $player->getServer()->getLogger()->error("Error in region delete: " . $e->getMessage());
                    $player->sendMessage("§c" . "Error al eliminar la región. Por favor, inténtalo de nuevo.");
                }
            }
        ));
    }

    public static function displayExistingRegions(Player $issuer)
    {
        $lang = Utils::getPluginFromIssuer($issuer)->resourceManager->getLanguagePack();
        $plugin = Utils::getPluginFromIssuer($issuer);
        $regions = array_keys($plugin->getRegions());
        
        if (empty($regions)) {
            $issuer->sendMessage("§c" . ($lang["gui_no_regions_error"] ?? "No hay regiones creadas aún. Crea una región primero."));
            return;
        }

        $issuer->sendForm(new CustomForm("§9§l" . $lang["gui_btn_rg_management"],
            [
                new Dropdown($lang["gui_dropdown_select_manage"], $regions),
            ],
            function (Player $player, CustomFormResponse $response) use ($plugin): void {
                try {
                    $values = $response->getValues();
                    $rgIndex = $values[0] ?? 0;
                    
                    $regions = array_keys($plugin->getRegions());
                    if (isset($regions[$rgIndex])) {
                        $rgName = $regions[$rgIndex];
                        self::displayRgEditing($player, $rgName);
                    } else {
                        $player->sendMessage("§c" . "Error: La región seleccionada no existe.");
                    }
                } catch (\Exception $e) {
                    $player->sendMessage("§c" . "Hubo un error al procesar tu selección. Por favor, inténtalo de nuevo.");
                    $player->getServer()->getLogger()->error("Error en WorldGuard GUI: " . $e->getMessage());
                }
            }
        ));
    }

    public static function displayRgEditing(Player $issuer, $rgName)
    {
        $plugin = Utils::getPluginFromIssuer($issuer);
        $regions = $plugin->getRegions();
        
        if (!isset($regions[$rgName])) {
            $issuer->sendMessage("§c" . "La región $rgName no existe.");
            return;
        }
        
        $rg = $regions[$rgName];
        self::$currentlyEditedRg = $rgName;

        $lang = $plugin->resourceManager->getLanguagePack();

        $toBool = function($value) {
            if (is_bool($value)) {
                return $value;
            }
            if (is_string($value)) {
                return strtolower($value) === "true" || $value === "1";
            }
            return (bool)$value;
        };

        try {
            $issuer->sendForm(new CustomForm($lang["gui_manage_menu_title"] . " §9" . $rgName,
                [
                    new Toggle($lang["gui_flag_pluginbypass"], $toBool($rg->getFlag("pluginbypass"))),
                    new Toggle($lang["gui_flag_deny_message"], $toBool($rg->getFlag("deny-msg"))),
                    new Toggle($lang["gui_flag_blockbreak"], $toBool($rg->getFlag("block-break"))),
                    new Toggle($lang["gui_flag_blockplace"], $toBool($rg->getFlag("block-place"))),
                    new Toggle($lang["gui_flag_pvp"], $toBool($rg->getFlag("pvp"))),
                    new Toggle($lang["gui_flag_xp_drops"], $toBool($rg->getFlag("exp-drops"))),
                    new Toggle($lang["gui_flag_invincible"], $toBool($rg->getFlag("invincible"))),
                    new Toggle($lang["gui_flag_fall_dmg"], $toBool($rg->getFlag("fall-dmg"))),
                    new Dropdown($lang["gui_flag_effect"], [$lang["gui_effect_delete"], $lang["gui_effect_speed"], $lang["gui_effect_slowness"],
                        $lang["gui_effect_haste"], $lang["gui_effect_fatigue"], $lang["gui_effect_strength"], $lang["gui_effect_healing"],
                        $lang["gui_effect_damage"], $lang["gui_effect_jump_boost"], $lang["gui_effect_nausea"], $lang["gui_effect_regeneration"],
                        $lang["gui_effect_resistance"], $lang["gui_effect_fire_resistance"], $lang["gui_effect_water_breathing"],
                        $lang["gui_effect_invisiblilty"], $lang["gui_effect_blindness"], $lang["gui_effect_night_vision"], $lang["gui_effect_hunger"],
                        $lang["gui_effect_weakness"], $lang["gui_effect_poison"], $lang["gui_effect_wither"], $lang["gui_effect_healthboost"],
                        $lang["gui_effect_absorption"], $lang["gui_effect_saturation"], $lang["gui_effect_leviatation"], $lang["gui_effect_fatal_poison"],
                        $lang["gui_effect_conduit_power"]]),
                    new Label($lang["gui_effect_restart_label"]),
                    new Toggle($lang["gui_flag_usage"], $toBool($rg->getFlag("use"))),
                    new Toggle($lang["gui_flag_interactframe"], $toBool($rg->getFlag("interactframe"))),
                    new Toggle($lang["gui_flag_item_drop"], $toBool($rg->getFlag("item-drop"))),
                    new Toggle($lang["gui_flag_item_death_drop"], $toBool($rg->getFlag("item-by-death"))),
                    new Toggle($lang["gui_flag_explosions"], $toBool($rg->getFlag("explosion"))),
                    new Input($lang["gui_flag_notify_enter"], (string)$rg->getFlag("notify-enter")),
                    new Input($lang["gui_flag_notify_leave"], (string)$rg->getFlag("notify-leave")),
                    new Toggle($lang["gui_flag_potions"], $toBool($rg->getFlag("potions"))),
                    new Toggle($lang["gui_flag_allowed_enter"], $toBool($rg->getFlag("allowed-enter"))),
                    new Toggle($lang["gui_flag_allowed_leave"], $toBool($rg->getFlag("allowed-leave"))),
                    new Dropdown($lang["gui_flag_gm"], ["Ignore", $lang["gui_gm_survival"], $lang["gui_gm_creative"], $lang["gui_gm_adventure"]]),
                    new Toggle($lang["gui_flag_sleep"], $toBool($rg->getFlag("sleep"))),
                    new Toggle($lang["gui_flag_send_chat"], $toBool($rg->getFlag("send-chat"))),
                    new Toggle($lang["gui_flag_rcv_chat"], $toBool($rg->getFlag("receive-chat"))),
                    new Toggle($lang["gui_flag_enderpearl"], $toBool($rg->getFlag("enderpearl"))),
                    new Toggle($lang["gui_flag_bow"], $toBool($rg->getFlag("bow"))),
                    new Dropdown($lang["gui_flag_fly_mode"], ["Vanilla", $lang["gui_enabled"], $lang["gui_disabled"], "Supervised"]),
                    new Toggle($lang["gui_flag_eat"], $toBool($rg->getFlag("eat"))),
                    new Toggle($lang["gui_flag_hunger"], $toBool($rg->getFlag("hunger"))),
                    new Toggle($lang["gui_flag_dmg_animals"], $toBool($rg->getFlag("allow-damage-animals"))),
                    new Toggle($lang["gui_flag_dmg_monsters"], $toBool($rg->getFlag("allow-damage-monsters"))),
                    new Toggle($lang["gui_flag_leaf_decay"], $toBool($rg->getFlag("allow-leaves-decay"))),
                    new Toggle($lang["gui_flag_plant_growth"], $toBool($rg->getFlag("allow-plant-growth"))),
                    new Toggle($lang["gui_flag_spread"], $toBool($rg->getFlag("allow-spreading"))),
                    new Toggle($lang["gui_flag_block_burn"], $toBool($rg->getFlag("allow-block-burn"))),
                    new Input($lang["gui_flag_priority"], (string)$rg->getFlag("priority"))
                ],
                function (Player $player, CustomFormResponse $response): void {
                    try {
                        $values = $response->getValues();
                        
                        if (count($values) < 30) {
                            $player->sendMessage("§c" . "Error al procesar el formulario: faltan datos.");
                            return;
                        }
                        
                        list($pluginBypass, $denyMessage, $blockBreak, $blockPlace, $pvpFlag, $xpFlag, $invincibleFlag, $fallDmgFlag, $effectsFlag,
                            $useFlag, $interactFrameFlag, $itemDropFlag, $itemDeathDropFlag, $explosionsFlag, $notifyEnterFlag, $notifyLeaveFlag, $potionsFlag,
                            $allowEnterFlag, $allowLeaveFlag, $gamemodeFlag, $sleepFlag, $sendChatFlag, $receiveChatFlag, $enderPearlFlag,
                            $bowFlag, $flyModeFlag, $eatingFlag, $HungerFlag, $damageAnimalsFlag, $damageMonstersFlag,
                            $leafDecayFlag, $plantGrowthFlag, $spreadingFlag, $blockBurnFlag, $priorityFlag) = $values;

                        $lang = Utils::getPluginFromIssuer($player)->resourceManager->getLanguagePack();
                        $console = new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage());
                        
                        $execCommand = function($command) use ($player, $console) {
                            try {
                                $player->getServer()->dispatchCommand($console, $command);
                            } catch (\Exception $e) {
                                $player->getServer()->getLogger()->error("Error executing command: $command - " . $e->getMessage());
                            }
                        };

                        $pluginBypass = is_bool($pluginBypass) ? $pluginBypass : ($pluginBypass === "true" || $pluginBypass === "1");
                        $denyMessage = is_bool($denyMessage) ? $denyMessage : ($denyMessage === "true" || $denyMessage === "1");
                        $blockBreak = is_bool($blockBreak) ? $blockBreak : ($blockBreak === "true" || $blockBreak === "1");
                        $blockPlace = is_bool($blockPlace) ? $blockPlace : ($blockPlace === "true" || $blockPlace === "1");
                        $pvpFlag = is_bool($pvpFlag) ? $pvpFlag : ($pvpFlag === "true" || $pvpFlag === "1");
                        $xpFlag = is_bool($xpFlag) ? $xpFlag : ($xpFlag === "true" || $xpFlag === "1");
                        $invincibleFlag = is_bool($invincibleFlag) ? $invincibleFlag : ($invincibleFlag === "true" || $invincibleFlag === "1");
                        $fallDmgFlag = is_bool($fallDmgFlag) ? $fallDmgFlag : ($fallDmgFlag === "true" || $fallDmgFlag === "1");
                        
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" pluginbypass " . var_export($pluginBypass, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" deny-msg " . var_export($denyMessage, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" block-break " . var_export($blockBreak, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" block-place " . var_export($blockPlace, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" pvp " . var_export($pvpFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" exp-drops " . var_export($xpFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" invincible " . var_export($invincibleFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" fall-dmg " . var_export($fallDmgFlag, true));
                        
                        $effectMap = [
                            $lang["gui_effect_delete"] => 0,
                            $lang["gui_effect_speed"] => 1,
                            $lang["gui_effect_slowness"] => 2,
                            $lang["gui_effect_haste"] => 3,
                            $lang["gui_effect_fatigue"] => 4,
                            $lang["gui_effect_strength"] => 5,
                            $lang["gui_effect_healing"] => 6,
                            $lang["gui_effect_damage"] => 7,
                            $lang["gui_effect_jump_boost"] => 8,
                            $lang["gui_effect_nausea"] => 9,
                            $lang["gui_effect_regeneration"] => 10,
                            $lang["gui_effect_resistance"] => 11,
                            $lang["gui_effect_fire_resistance"] => 12,
                            $lang["gui_effect_water_breathing"] => 13,
                            $lang["gui_effect_invisiblilty"] => 14,
                            $lang["gui_effect_blindness"] => 15,
                            $lang["gui_effect_night_vision"] => 16,
                            $lang["gui_effect_hunger"] => 17,
                            $lang["gui_effect_weakness"] => 18,
                            $lang["gui_effect_poison"] => 19,
                            $lang["gui_effect_wither"] => 20,
                            $lang["gui_effect_healthboost"] => 21,
                            $lang["gui_effect_absorption"] => 22,
                            $lang["gui_effect_saturation"] => 23,
                            $lang["gui_effect_leviatation"] => 24,
                            $lang["gui_effect_fatal_poison"] => 25,
                            $lang["gui_effect_conduit_power"] => 26
                        ];
                        
                        $effectValue = $effectMap[$effectsFlag] ?? 0;
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" effects " . $effectValue);
                        
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" use " . var_export($useFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" interactframe " . var_export($interactFrameFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" item-drop " . var_export($itemDropFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" item-by-death " . var_export($itemDeathDropFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" explosion " . var_export($explosionsFlag, true));
                        if ($notifyEnterFlag != '' || $notifyEnterFlag != ' ') {
                            $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" notify-enter " . $notifyEnterFlag);
                        }
                        if ($notifyLeaveFlag != '' || $notifyLeaveFlag != ' ') {
                            $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" notify-leave " . $notifyLeaveFlag);
                        }
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" potions " . var_export($potionsFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" allowed-enter " . var_export($allowEnterFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" allowed-leave " . var_export($allowLeaveFlag, true));
                        switch ($gamemodeFlag) {
                            case "Ignore":
                                $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" game-mode ignore");
                                break;
                            case $lang["gui_gm_survival"]:
                                $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" game-mode survival");
                                break;
                            case $lang["gui_gm_creative"]:
                                $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" game-mode creative");
                                break;
                            case $lang["gui_gm_adventure"]:
                                $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" game-mode adventure");
                                break;
                        }
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" sleep " . var_export($sleepFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" send-chat " . var_export($sendChatFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" receive-chat " . var_export($receiveChatFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" enderpearl " . var_export($enderPearlFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" bow " . var_export($bowFlag, true));
                        switch ($flyModeFlag) {
                            case "Vanilla":
                                $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" fly-mode 0");
                                break;
                            case $lang["gui_enabled"]:
                                $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" fly-mode 1");
                                break;
                            case $lang["gui_disabled"]:
                                $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" fly-mode 2");
                                break;
                            case "Supervised":
                                $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" fly-mode 3");
                                break;
                        }
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" eat " . var_export($eatingFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" hunger " . var_export($HungerFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" allow-damage-animals " . var_export($damageAnimalsFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" allow-damage-monsters " . var_export($damageMonstersFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" allow-leaves-decay " . var_export($leafDecayFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" allow-plant-growth " . var_export($plantGrowthFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" allow-spreading " . var_export($spreadingFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" allow-block-burn " . var_export($blockBurnFlag, true));
                        $execCommand("rg flags set \"" . self::$currentlyEditedRg . "\" priority " . intval($priorityFlag));
                        $player->sendMessage(TF::GREEN . "Region " . self::$currentlyEditedRg . " updated successfully!");
                        self::$currentlyEditedRg = "";
                    } catch (\Exception $e) {
                        $player->getServer()->getLogger()->error("Error updating region: " . $e->getMessage());
                        $player->sendMessage("§c" . "Error al actualizar la región. Por favor, inténtalo de nuevo.");
                    }
                }
            ));
        } catch (\Exception $e) {
            $issuer->getServer()->getLogger()->error("Error displaying region editing form: " . $e->getMessage());
            $issuer->sendMessage("§c" . "Error al mostrar el formulario de edición. Por favor, inténtalo de nuevo.");
        }
    }

    public static function displayHelpMenu(Player $issuer)
    {
        $plugin = Utils::getPluginFromIssuer($issuer);
        $lang = $plugin->resourceManager->getLanguagePack();

        $issuer->sendForm(new CustomForm("§9§l" . $lang["gui_btn_help"],
            [
                new Label($lang["gui_help_menu_label1"]),
                new Label($lang["gui_help_menu_label2"]),
            ],
            function (Player $player, CustomFormResponse $response): void {
                // Empty callback as this is just an informational form
            }
        ));
    }
}
