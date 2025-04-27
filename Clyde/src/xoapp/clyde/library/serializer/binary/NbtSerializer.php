<?php

namespace xoapp\clyde\library\serializer\binary;

use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\tag\Tag;
use pocketmine\nbt\TreeRoot;

use function bin2hex;
use function hex2bin;
use function zlib_decode;
use function zlib_encode;

use const ZLIB_ENCODING_GZIP;

final class NbtSerializer {

    public static function toBinary(Tag $tag, bool $z_gip = false): string {
        $contents = (new BigEndianNbtSerializer())->write(new TreeRoot($tag));
        return $z_gip ? zlib_encode($contents, ZLIB_ENCODING_GZIP) : $contents;
    }

    public static function fromBinary(string $contents, bool $z_gip = false): Tag {
        return (new BigEndianNbtSerializer())->read($z_gip ? zlib_decode($contents) : $contents)->getTag();
    }

    public static function toHex(Tag $tag, bool $z_gip = false): string {
        return bin2hex(self::toBinary($tag, $z_gip));
    }

    public static function fromHex(string $contents, bool $z_gip = false): Tag {
        return self::fromBinary(hex2bin($contents), $z_gip);
    }
}