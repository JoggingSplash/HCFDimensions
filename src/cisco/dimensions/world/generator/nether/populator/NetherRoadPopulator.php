<?php

namespace cisco\dimensions\world\generator\nether\populator;

use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\populator\Populator;

class NetherRoadPopulator implements Populator {


	public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void    {
		$realX = $chunkX << 4;
		$realZ = $chunkZ << 4;

		for ($ix = 0; $ix < 16; $ix++) {
			$x = $realX + $ix;
			for ($iz = 0; $iz < 16; $iz++) {
				$z = $realZ + $iz;

				$dist = sqrt(($x * $x) + ($z * $z));

				if ($dist >= 128 && abs($x) > 8 && abs($z) > 8){
					continue;
				}

				for ($iy = 0; $iy <= 32; $iy++) {
					try{
						$world->setBlockAt($x, $iy, $z, ($iy > 30 && (abs($x) <= 8 || abs($z) <= 8)) ? VanillaBlocks::OBSIDIAN() : VanillaBlocks::NETHERRACK()); // better option

					}catch(\Throwable $t){

					}
				}
			}
		}
	}
}