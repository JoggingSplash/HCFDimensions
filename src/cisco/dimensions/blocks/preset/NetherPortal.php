<?php

namespace cisco\dimensions\blocks\preset;

use cisco\dimensions\event\PlayerDimensionChangeEvent;
use cisco\dimensions\Loader;
use cisco\dimensions\utils\TestValidTimelapse;
use cisco\dimensions\world\WorldManager;
use pocketmine\block\NetherPortal as PMNetherPortal;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use pocketmine\world\format\Chunk;
use pocketmine\world\Position;

class NetherPortal extends PMNetherPortal {
	public function hasEntityCollision(): bool{
		return true;
	}

	public function onEntityInside(Entity $entity): bool    {
		if(!$entity instanceof Player or !$entity->isOnline()){
			return false;
		}

		if($entity->getWorld() === WorldManager::NETHER()){
			$position = WorldManager::DEFAULT()->getSpawnLocation()->asPosition();
		}else{
			$position = WorldManager::NETHER()->getSpawnLocation()->asPosition();
		}

        if(PlayerDimensionChangeEvent::hasHandlers()){
            $ev = new PlayerDimensionChangeEvent(
                $entity,
                $entity->getWorld(),
                $position
            );

            $ev->call();

            if($ev->isCancelled()){
                return false;
            }

            $position = $ev->getTo();
        }

        $position->getWorld()->loadChunk(
            $position->getFloorX() >> Chunk::COORD_BIT_SIZE,
            $position->getFloorZ() >> Chunk::COORD_BIT_SIZE
        );

		return TestValidTimelapse::test($entity, $position);
	}
}