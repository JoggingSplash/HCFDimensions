<?php

namespace cisco\stp\display;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;

final class EntityUtils {

    private function __construct() { }

    /**
     * @param Location $location
     * @param Player[] $viewers
     * @param string $entityId {@link EntityIds}
     * @return void
     */
    public static function spawn(Location $location, array $viewers, string $entityId): void {
        $id = Entity::nextRuntimeId();
        $packet = AddActorPacket::create(
            $id,
            $id,
            $entityId,
            $location->asVector3(),
            null,
            $location->yaw,
            $location->pitch,
            0.0,
            0.0,
            [],
            [],
            new PropertySyncData([], []),
            []
        );

        foreach ($viewers as $viewer) {
            $viewer->getNetworkSession()->sendDataPacket($packet);
        }
    }
}