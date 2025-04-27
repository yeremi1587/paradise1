<?php

namespace xoapp\clyde\forms;

use xoapp\clyde\profile\Profile;
use pocketmine\utils\TextFormat;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\block\utils\MobHeadType;
use xoapp\clyde\library\muqsit\invmenu\InvMenu;
use xoapp\clyde\library\muqsit\invmenu\transaction\InvMenuTransaction;
use xoapp\clyde\library\muqsit\invmenu\transaction\InvMenuTransactionResult;
use xoapp\clyde\library\muqsit\invmenu\type\InvMenuTypeIds;
use xoapp\clyde\utils\Prefixes;

class InventoryManager
{

    public static function openInventory(Player $player, Player $i_player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);

        $menu->setName(
            $i_player->getName() . "'s Inventory"
        );

        $inventory = $menu->getInventory();

        $inventory->setContents(
            $i_player->getInventory()->getContents()
        );

        $extra = VanillaBlocks::STAINED_GLASS()
            ->setColor(DyeColor::BLACK)
            ->asItem();

        $extra->setNamedTag(
            CompoundTag::create()->setString("extra", "")
        );

        for ($i = 28; $i <= 53; $i++) {
            if (in_array($i, [47, 48, 50, 51])) {
                continue;
            }

            $inventory->setItem($i, $extra);
        }

        $v_armor = $i_player->getArmorInventory();

        $helmet = is_null($v_armor->getHelmet()) ? VanillaItems::AIR() : $v_armor->getHelmet();
        $chestplate = is_null($v_armor->getChestplate()) ? VanillaItems::AIR() : $v_armor->getChestplate();
        $leggings = is_null($v_armor->getLeggings()) ? VanillaItems::AIR() : $v_armor->getLeggings();
        $boots = is_null($v_armor->getBoots()) ? VanillaItems::AIR() : $v_armor->getBoots();

        $inventory->setItem(47, $helmet);
        $inventory->setItem(48, $chestplate);
        $inventory->setItem(50, $leggings);
        $inventory->setItem(51, $boots);

        $menu->setListener(InvMenu::readonly());

        $menu->send($player);
    }

    public static function openEnderInventory(Player $player, Player $i_player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);

        $menu->setName(
            $i_player->getName() . "'s Ender Chest"
        );

        $inventory = $menu->getInventory();

        $inventory->setContents(
            $i_player->getEnderInventory()->getContents()
        );

        $menu->setListener(InvMenu::readonly());

        $menu->send($player);
    }

    public static function getProfileData(Player $player, Profile $profile): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName($profile->getName() . " Saved Data");

        $saved_data = $profile->getSavedData();
        foreach ($saved_data as $key => $data) {

            $item = VanillaBlocks::MOB_HEAD()
                ->setMobHeadType(MobHeadType::PLAYER)
                ->asItem();

            $item->setNamedTag(
                CompoundTag::create()->setInt("id", intval($key))
            );

            $item->setCustomName(
                TextFormat::colorize("&eSaved Data #" . intval($key))
            );

            $item->setLore(array_map(
                fn (string $line) => TextFormat::colorize($line),
                [
                    " ",
                    " &fCreation Date: &a" . $data["date"],
                    " &fKiller: &a" . $data["killer"],
                    " ",
                    " &fInventory Contents: &e" . sizeof($data["contents"]),
                    " &fArmor Contents: &e" . sizeof($data["armor"]),
                    " &fOffHand Contents: &e" . sizeof($data["offhand"]),
                    " ",
                    "&7&o Tap to send data "
                ]
            ));

            $menu->getInventory()->addItem($item);
        }

        $menu->setListener(
            function (InvMenuTransaction $transaction) use ($profile): InvMenuTransactionResult {

                $player = $transaction->getPlayer();

                $item = $transaction->getItemClicked();

                $nbt = $item->getNamedTag()->getTag("id");
                if (is_null($nbt)) {
                    return $transaction->discard();
                }

                $id = intval($nbt->getValue());

                $data = $profile->getLog($id);
                if (is_null($data)) {
                    return $transaction->discard();
                }

                $player->removeCurrentWindow();

                $player->sendMessage(
                    Prefixes::GLOBAL . "You have successfully returned the §eRollback #" . $id . "§7 to player §a" . $profile->getName()
                );

                $profile->giveData($id);

                return $transaction->discard();
            }
        );

        $menu->send($player);
    }
}