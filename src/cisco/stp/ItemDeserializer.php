<?php

namespace cisco\stp;

use pocketmine\item\Item;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\world\format\io\GlobalItemDataHandlers;

final class ItemDeserializer {

    private static LittleEndianNbtSerializer $deserializer;

    /**
     * @param string|array $data
     * @return Item
     */
    static public function deserializeItem(string|array $data): Item     {
        if(is_array($data)){
            return self::deserializeJson($data);
        }

        $deserializer = self::$deserializer ??= new LittleEndianNbtSerializer();
        return Item::nbtDeserialize($deserializer->read(base64_decode($data))->mustGetCompoundTag());
    }

    /**
     * @param array $data
     * @return array
     */
    static public function deserializeItems(array $data): array {
        return array_map([self::class, 'deserializeItem'], $data);
    }

    /**
     * @param array $data
     * @return Item
     */
    static protected function deserializeJson(array $data): Item    {
        if(isset($data["nbt"])){
            $nbt = $data["nbt"];
        }elseif(isset($data["nbt_hex"])){
            $nbt = hex2bin($data["nbt_hex"]);
        }elseif(isset($data["nbt_b64"])){
            $nbt = base64_decode($data["nbt_b64"], true);
        }else {
            $nbt = "";
        }

        $deserializer = self::$deserializer ??= new LittleEndianNbtSerializer();
        return GlobalItemDataHandlers::getDeserializer()->deserializeStack(
            GlobalItemDataHandlers::getUpgrader()->upgradeItemTypeDataInt(
                (int) $data["id"],
                (int) ($data["damage"] ?? 0),
                (int) ($data["count"] ?? 1),
                $nbt !== "" ? $deserializer->read($nbt)->mustGetCompoundTag() : null
            )
        );
    }
}