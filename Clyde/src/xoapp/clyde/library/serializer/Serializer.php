<?php

namespace xoapp\clyde\library\serializer;

use pocketmine\Server;
use pocketmine\world\World;
use InvalidArgumentException;
use pocketmine\item\Item;
use pocketmine\world\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;
use xoapp\clyde\library\serializer\binary\NbtSerializer;

class Serializer {

    public static function serialize(array $contents): string {

        if (empty($contents)) {
            return "";
        }

        $result = [];

        foreach ($contents as $slot => $content) {
            /** @var Item $content */
            $result[$slot] = self::serializeItem($content);
        }

        return serialize($result);
    }

    public static function deserialize(string $contents): array {
        $contents = unserialize($contents);

        if (!is_array($contents)) {
            return [];
        }

        if (empty($contents)) {
            return [];
        }

        $result = [];

        foreach ($contents as $slot => $content) {
            $result[$slot] = self::deserializeItem($content);
        }

        return $result;
    }

    public static function positionToString(?Position $position): ?string {
        return is_null($position) ? null : $position->getX() . ":" .
            $position->getY() . ":" .
            $position->getZ() . ":" .
            $position->getWorld()->getFolderName();
    }

    public static function stringToPosition(?string $string): ?Position {

        if (is_null($string)) {
            return null;
        }

        $data = explode(":", $string);

        return new Position(
            $data[0], $data[1], $data[2], self::getWorld($data[3])
        );
    }

    private static function getWorld(string $name): ?World {
        return Server::getInstance()->getWorldManager()->getWorldByName($name);
    }

    public static function serializeItem(Item $item): string {
        return self::encodeToUTF8(NbtSerializer::toHex($item->nbtSerialize()));
    }

    public static function deserializeItem(?string $contents): ?Item {

        if (is_null($contents)) {
            return null;
        }

        return self::deserializeItemTag(NbtSerializer::fromHex($contents));
    }

    private static function encodeToUTF8(string $contents): string {
        return mb_convert_encoding($contents, "UTF-8", mb_detect_encoding($contents));
    }

    private static function deserializeItemTag(Tag $tag): Item {

        if (!$tag instanceof CompoundTag) {
            throw new InvalidArgumentException("Invalid tag type : " . get_class($tag));
        }

        return Item::nbtDeserialize($tag);
    }
}