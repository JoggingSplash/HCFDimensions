<?php

namespace cisco\stp;

use pocketmine\item\Item;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;

final class ItemSerializer {

    private static LittleEndianNbtSerializer $serializer;


    /**
     * @param Item $item
     * @return string
     */
    static public function serializeItem(Item $item): string {
        $serializer = self::$serializer ??= new LittleEndianNbtSerializer();
        $root = new TreeRoot($item->nbtSerialize());
        return base64_encode($serializer->write($root));
    }

    /**
     * @param Item ...$items
     * @return array
     */
    static public function serializeItems(Item ...$items): array {
        return array_map([self::class, 'serializeItem'], $items);
    }
}