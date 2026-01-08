<?php

namespace cisco\stp;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;

final class ServerUtils {


    /**
     * @param Vector3 $position
     * @param Block $block
     * @return UpdateBlockPacket
     */
    public static function createFakeBlock(Vector3 $position, Block $block): UpdateBlockPacket    {
        $pos = BlockPosition::fromVector3($position->asVector3());
        $bid = TypeConverter::getInstance()->getBlockTranslator()->internalIdToNetworkId($block->getStateId());
        return UpdateBlockPacket::create($pos, $bid, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
    }

    static public function titleIdToOs(?string $titleId): int    {
        return match ($titleId) {
            '1739947436' => DeviceOS::ANDROID,
            '1810924247' => DeviceOS::IOS,
            '1944307183' => DeviceOS::AMAZON,
            '896928775' => DeviceOS::WINDOWS_10,
            '2044456598' => DeviceOS::PLAYSTATION,
            '2047319603' => DeviceOS::NINTENDO,
            '1828326430', null => DeviceOS::XBOX, // null can also be PS but its ok
            default => DeviceOS::UNKNOWN
        };
    }

    static public function titleIdToOsString(?string $titleId): string    {
        return match(self::titleIdToOs($titleId)){
            DeviceOS::ANDROID => "Android",
            DeviceOS::IOS => "iOS",
            DeviceOS::XBOX => "Xbox",
            DeviceOS::AMAZON => "Amazon",
            DeviceOS::WINDOWS_10 => "Windows 10",
            DeviceOS::PLAYSTATION => "PlayStation",
            DeviceOS::NINTENDO => "Nintendo",
            default => "Unknown"
        };
    }


}