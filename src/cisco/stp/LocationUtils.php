<?php

namespace cisco\stp;

use pocketmine\entity\Location;
use pocketmine\Server;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\world\WorldManager;

final class LocationUtils {

    protected static WorldManager $manager;

    static public function toString(Location $location): string {
        return "$location->x:$location->y:$location->z:{$location->getWorld()->getFolderName()}:$location->yaw:$location->pitch";
    }

    static public function fromString(string $string): Location {
        $arguments = explode(":", $string);

        if(count($arguments) !== 6) {
            throw new AssumptionFailedError("Expected 6 arguments on the location string, got " . count($arguments));
        }

        $world = $arguments[3];
        $manager = self::$manager ??= Server::getInstance()->getWorldManager();
        if(!$manager->isWorldLoaded($world) && !$manager->loadWorld($world)) {
            throw new AssumptionFailedError("Word $world could not be loaded or not found");
        }

        return new Location(
            $arguments[0],
            $arguments[1],
            $arguments[2],
            $manager->getWorldByName($world),
            (float) $arguments[4],
            (float) $arguments[5]
        );
    }

    static public function withComponents(Location $location, ?float $x, ?float $y, ?float $z, ?float $yaw, ?float $pitch): Location    {
        return Location::fromObject($location->withComponents($x, $y, $z), $location->getWorld(), $yaw ?? $location->getYaw(), $pitch ?? $location->getPitch());
    }

    /**
     * @param Location $location
     * @return Location
     */
    static public function floor(Location $location): Location {
        return Location::fromObject($location->floor(), $location->getWorld(), floor($location->yaw), floor($location->pitch));
    }


    /**
     * @param Location $location
     * @return Location
     */
    static public function ceil(Location $location): Location {
        return Location::fromObject($location->ceil(), $location->getWorld(), ceil($location->yaw), ceil($location->pitch));
    }

}