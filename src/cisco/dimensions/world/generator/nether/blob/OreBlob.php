<?php

namespace cisco\dimensions\world\generator\nether\blob;

use pocketmine\block\BlockTypeIds;
use pocketmine\math\VectorMath;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\OreType;

class OreBlob {


	public function __construct(private Random $random, public OreType $type, private array $canOverride = [BlockTypeIds::STONE]) {

	}

	public function getType(): OreType {
		return $this->type;
	}

	public function canPlaceObject(ChunkManager $level, int $x, int $y, int $z): bool {
		return in_array($level->getBlockAt($x, $y, $z)->getTypeId(), $this->canOverride, true);
	}

	public function placeObject(ChunkManager $manager, int $x, int $y, int $z) {
		$clusterSize = $this->type->clusterSize;
		$angle = $this->random->nextFloat() * M_PI;
		$offset = VectorMath::getDirection2D($angle)->multiply($clusterSize / 8);
		$x1 = $x + 8 + $offset->x;
		$x2 = $x + 8 - $offset->x;
		$z1 = $z + 8 + $offset->y;
		$z2 = $z + 8 - $offset->y;
		$y1 = $y + $this->random->nextBoundedInt(3) + 2;
		$y2 = $y + $this->random->nextBoundedInt(3) + 2;
		for($count = 0; $count <= $clusterSize; ++$count) {
			$seedX = $x1 + ($x2 - $x1) * $count / $clusterSize;
			$seedY = $y1 + ($y2 - $y1) * $count / $clusterSize;
			$seedZ = $z1 + ($z2 - $z1) * $count / $clusterSize;
			$size = ((sin($count * (M_PI / $clusterSize)) + 1) * $this->random->nextFloat() * $clusterSize / 16 + 1) / 2;

			$startX = (int)($seedX - $size);
			$startY = (int)($seedY - $size);
			$startZ = (int)($seedZ - $size);
			$endX = (int)($seedX + $size);
			$endY = (int)($seedY + $size);
			$endZ = (int)($seedZ + $size);

			for($x = $startX; $x <= $endX; ++$x) {
				$sizeX = ($x + 0.5 - $seedX) / $size;
				$sizeX *= $sizeX;

				if($sizeX < 1) {
					for($y = $startY; $y <= $endY; ++$y) {
						$sizeY = ($y + 0.5 - $seedY) / $size;
						$sizeY *= $sizeY;

						if($y > 0 and ($sizeX + $sizeY) < 1) {
							for($z = $startZ; $z <= $endZ; ++$z) {
								$sizeZ = ($z + 0.5 - $seedZ) / $size;
								$sizeZ *= $sizeZ;

								if(($sizeX + $sizeY + $sizeZ) < 1 and in_array($manager->getBlockAt($x, $y, $z)->getTypeId(), $this->canOverride, true)) {
									$manager->setBlockAt($x, $y, $z, $this->type->material);
								}
							}
						}
					}
				}
			}
		}
	}
}