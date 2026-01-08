<?php

namespace cisco\dimensions\world\generator\nether\populator;

use cisco\dimensions\world\generator\nether\blob\OreBlob;
use pocketmine\block\BlockTypeIds;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\OreType;
use pocketmine\world\generator\populator\Ore;

class FlexibleGroundPopulator extends Ore{

	/** @var OreType[] */
	protected array $oreTypes = [];
	protected array $overwritable;

	public function __construct(array $overwritable = [BlockTypeIds::STONE]){
		$this->overwritable = $overwritable;
	}

	public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random) : void{
		foreach($this->oreTypes as $type){
			$ore = new OreBlob($random, $type, $this->overwritable);
			$x = $y = $z = 0;
			for($i = 0; $i < $ore->type->clusterCount; ++$i){
				if(!self::getRandomXYZ($world, $type, $random, $chunkX, $chunkZ, $x, $y, $z)){
					continue;
				}
				if($ore->canPlaceObject($world, $x, $y, $z)){
					$ore->placeObject($world, $x, $y, $z);
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
	) : bool{
		$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
		$y = $random->nextRange($type->minHeight, $type->maxHeight);
		$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
		return true;
	}

	/**
	 * @param OreType[] $types
	 */
	public function setOreTypes(array $types) : void{
		$this->oreTypes = $types;
	}
}