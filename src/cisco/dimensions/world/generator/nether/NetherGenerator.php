<?php

namespace cisco\dimensions\world\generator\nether;

use cisco\dimensions\world\generator\nether\populator\FlexibleGroundPopulator;
use cisco\dimensions\world\generator\nether\populator\FlexibleHangingPopulator;
use cisco\dimensions\world\generator\nether\populator\NetherRoadPopulator;
use cisco\dimensions\world\generator\nether\populator\RandomGroundPopulator;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\world\biome\BiomeRegistry;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\noise\Simplex;
use pocketmine\world\generator\object\OreType;
use pocketmine\world\generator\populator\Populator;

class NetherGenerator extends Generator {

	/** @var Populator[] */
	protected array $populators = [];
	protected int $waterHeight = 32;
	protected int $emptyHeight = 64;
	protected int $emptyAmplitude = 1;
	protected float $density = 0.7;
	/** @var Populator[] */
	protected array $generationPopulators = [];
	protected Simplex $noiseBase;

	public function __construct(int $seed, string $preset){
		parent::__construct($seed, $preset);

		$this->random->setSeed($seed);
		$this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 64);
		$this->random->setSeed($seed);

		$this->populators[] = $gFire = new RandomGroundPopulator(
			VanillaBlocks::FIRE(), [BlockTypeIds::NETHERRACK], [BlockTypeIds::AIR], 64
		);
		$gFire->setBaseAmount(1);
		$gFire->setRandomAmount(1);

		$this->populators[] = $nOres = new FlexibleGroundPopulator([BlockTypeIds::NETHERRACK]);
		$nOres->setOreTypes([
			new OreType(VanillaBlocks::NETHER_QUARTZ_ORE(), VanillaBlocks::NETHERRACK(), 20, 16, 0, 128),
			new OreType(VanillaBlocks::SOUL_SAND(), VanillaBlocks::NETHERRACK(), 5, 64, 0, 128),
			new OreType(VanillaBlocks::GRAVEL(), VanillaBlocks::NETHERRACK(), 5, 64, 0, 128),
			new OreType(VanillaBlocks::LAVA(), VanillaBlocks::NETHERRACK(), 1, 16, 0, 32),
		]);

		$this->populators[] = $nOres = new FlexibleHangingPopulator([BlockTypeIds::AIR]);
		$nOres->setOreTypes([
			new OreType(VanillaBlocks::GLOWSTONE(), VanillaBlocks::AIR(), 1, 20, 64, 126),
		]);

		$this->generationPopulators[] = new NetherRoadPopulator();
	}

	private function circleGrad(float $dist, int $centerRadius, int $totalRadius, float $amplitude = 1): ?float {
		if($dist > $totalRadius) return null;
		if($dist < $centerRadius) return $amplitude;
		if($centerRadius != $totalRadius) {
			return (1 - (($dist - $centerRadius) / ($totalRadius - $centerRadius))) * $amplitude;
		}
		return (1 - ($dist / $centerRadius)) * $amplitude;
	}

	protected function getDensity(int $x, int $y, int $z): float {
		$default = 0.7;
		$weights = [0];

		$clearAmp = 1.25;

		$dist = sqrt(($x * $x) + ($z * $z));
		if(($spawnW = $this->circleGrad($dist, 128, 128, $clearAmp)) !== null) {
			$weights[] = $spawnW;
		}

		if($y < 64) $clearAmp = 1.5;
		$xDist = abs($x);
		$zDist = abs($z);

		if(($xRoad = $this->circleGrad($xDist, 4, 8, $clearAmp)) !== null) {
			$weights[] = $xRoad;
		}

		if(($zRoad = $this->circleGrad($zDist, 4, 8, $clearAmp)) !== null) {
			$weights[] = $zRoad;
		}

		return (1 - max($weights)) * $default;
	}

	public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);

		$noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

		$chunk = $world->getChunk($chunkX, $chunkZ);

		if($chunk === null){
			return;
		}

		$bedrock = VanillaBlocks::BEDROCK()->getStateId();
		$netherrack = VanillaBlocks::NETHERRACK()->getStateId();
		$lava = VanillaBlocks::LAVA()->setStill()->getStateId();

		for ($x = 0; $x < 16; ++$x) {
			for ($z = 0; $z < 16; ++$z) {
				for ($y = 0; $y < 128; ++$y) {
					$biome = BiomeRegistry::getInstance()->getBiome(BiomeIds::HELL);
					$chunk->setBiomeId($x, $y, $z, $biome->getId());

					if ($y === 0 || $y === 127) {
						$chunk->setBlockStateId($x, $y, $z, $bedrock);
						continue;
					}

					$noiseValue = (abs($this->emptyHeight - $y) / $this->emptyHeight) * $this->emptyAmplitude - ($noise[$x][$z][$y] ?? 0);
					$noiseValue -= 1 - $this->getDensity(($chunkX << 4) + $x, $y, ($chunkZ << 4) + $z);

					if ($noiseValue > 0) {
						$chunk->setBlockStateId($x, $y, $z, $netherrack);
					} elseif ($y <= $this->waterHeight) {
						$chunk->setBlockStateId($x, $y, $z, $lava);
					}
				}
			}
		}

		foreach ($this->generationPopulators as $populator) {
			$populator->populate($world, $chunkX, $chunkZ, $this->random);
		}
	}

	public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);

		foreach ($this->populators as $populator) {
			$populator->populate($world, $chunkX, $chunkZ, $this->random);
		}

		$chunk = $world->getChunk($chunkX, $chunkZ);
		$biome = BiomeRegistry::getInstance()->getBiome($chunk->getBiomeId(7, 7, 7));
		$biome->populateChunk($world, $chunkX, $chunkZ, $this->random);
	}
}