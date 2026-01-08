<?php

namespace cisco\dimensions\world\generator\nether\populator;

use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\populator\Populator;

class RandomGroundPopulator implements Populator{

	private ChunkManager $manager;
	private int $randomAmount;
	private int $baseAmount;


	public function __construct(
		private Block $block,
		private array $canStayOn = [BlockTypeIds::GRASS],
		private array $ignoredBlocks = [BlockTypeIds::AIR, BlockTypeIds::SNOW_LAYER],
		private int $maxHeight = 127
	){

	}

	public function setRandomAmount(int $amount): void {
		$this->randomAmount = $amount;
	}

	public function setBaseAmount(int $amount): void {
		$this->baseAmount = $amount;
	}

	public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		$this->manager = $world;
		$amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
		for($i = 0; $i < $amount; ++$i) {
			$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
			$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
			$y = $this->getHighestWorkableBlock($x, $z);

			if($y !== -1 and $this->canBlockStay($x, $y, $z)) {
				$this->manager->setBlockAt($x, $y, $z, $this->block);
			}
		}
	}

	private function getHighestWorkableBlock(int $x, int $z): int {
		for($y = $this->maxHeight; $y >= 0; --$y) {
			$b = $this->manager->getBlockAt($x, $y, $z)->getTypeId();
			if(!in_array($b, $this->ignoredBlocks)) {
				break;
			}
		}

		return $y === 0 ? -1 : ++$y;
	}

	private function canBlockStay(int $x, int $y, int $z): bool {
		$b = $this->manager->getBlockAt($x, $y, $z)->getTypeId();

		return in_array($b, $this->ignoredBlocks, true) and in_array($this->manager->getBlockAt($x, $y - 1, $z)->getTypeId(), $this->canStayOn, true);
	}
}