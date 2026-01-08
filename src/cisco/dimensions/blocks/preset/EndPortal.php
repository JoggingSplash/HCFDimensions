<?php

namespace cisco\dimensions\blocks\preset;

use cisco\dimensions\event\PlayerDimensionChangeEvent;
use cisco\dimensions\Loader;
use cisco\dimensions\utils\TestValidTimelapse;
use cisco\dimensions\world\WorldManager;
use pocketmine\block\Transparent;
use pocketmine\entity\Entity;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\format\Chunk;
use pocketmine\world\Position;

class EndPortal extends Transparent {

	public function getLightLevel() : int{
		return 15;
	}

	public function hasEntityCollision(): bool    {
		return true;
	}

	protected function recalculateCollisionBoxes() : array{
		return [AxisAlignedBB::one()->trim(Facing::UP, 0.25)];
	}

	public function onEntityInside(Entity $entity): bool{
		if(!$entity instanceof Player){
			return false;
		}

		if($entity->getWorld() === WorldManager::END()){
			$position = Position::fromObject(new Vector3(0, 80, 350),
				WorldManager::DEFAULT()
			);
		}else {
			$position = WorldManager::END()->getSpawnLocation()->asPosition();
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

        return TestValidTimelapse::test($entity, $position);
	}
}