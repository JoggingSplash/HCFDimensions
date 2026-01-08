<?php

namespace cisco\dimensions\world\generator\nether\populator;

use cisco\dimensions\world\generator\nether\blob\OreBlob;
use CortexPE\std\math\BezierCurve;
use pocketmine\block\BlockTypeIds;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\OreType;

class FlexibleHangingPopulator extends FlexibleGroundPopulator{

	public function __construct(array $overwritable = [BlockTypeIds::STONE]) {
		parent::__construct($overwritable);
	}

	public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		foreach($this->oreTypes as $type) {
			$ore = new OreBlob($random, $type, $this->overwritable);
			$x = $y = $z = 0;
			for($i = 0; $i < $ore->type->clusterCount; ++$i) {
				if(!static::getRandomXYZ($world, $type, $random, $chunkX, $chunkZ, $x, $y, $z)) continue;

				$points = [];
				$lastPoint = (new Vector3($x, $y, $z))->add(0.5, 0.5, 0.5); // center
				$pCount = $random->nextInt() % 3;
				for($p = 0; $p < $pCount; $p++) {
					$lastPoint = $points[] = $lastPoint->add(
						($random->nextSignedFloat() - 1) * 2,
						($random->nextSignedFloat() - 1) * 2,
						($random->nextSignedFloat() - 1) * 2,
					);
				}
				$curve = new BezierCurve($points);
				for($p = 0; $p < $pCount; $p++) {
					$center = $curve->getPoint($p * (1 / $pCount));
					$radius = $random->nextInt() % 3;
					$maxDist = $radius * $radius;
					for($ix = -$radius; $ix <= $radius; $ix++) {
						for($iy = -$radius; $iy <= $radius; $iy++) {
							for($iz = -$radius; $iz <= $radius; $iz++) {
								$d2 = ($ix * $ix) + ($iy * $iy) + ($iz * $iz);
								if($d2 > $maxDist) continue;
								$world->setBlockAt(
									(int)floor($center->x + $ix),
									(int)floor($center->y + $iy),
									(int)floor($center->z + $iz),
									$type->material
								);
							}
						}
					}
				}
			}
		}
	}

	protected function getRandomXYZ(
		ChunkManager $manager,
		OreType $type,
		Random $random,
		int $chunkX,
		int $chunkZ,
		int &$x,
		int &$y,
		int &$z
	): bool {
		$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
		$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
		for($y = $type->maxHeight; $y >= $type->minHeight; $y--) {
			if(in_array($manager->getBlockAt($x, $y, $z)->getTypeId(), $this->overwritable)) {
				return true;
			}
		}
		return false;
	}
}