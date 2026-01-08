<?php

namespace cisco\dimensions\blocks\preset;

use cisco\dimensions\blocks\DimensionBlocks;
use cisco\dimensions\item\preset\EnderEye;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\EndPortalFrame as PMEndPortalFrame;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class EndPortalFrame extends PMEndPortalFrame{

	public function onBreak(Item $item, ?Player $player = null, array &$returnedItems = []): bool    {
		if($this->hasEye()){
			foreach(Facing::ALL as $face){
				if(($block = $this->getSide($face)) instanceof EndPortal){
					$block->onBreak($item, $player);
				}
			}
		}
		return parent::onBreak($item, $player);
	}

	/**
	 * @param Item        $item
	 * @param int         $face
	 * @param Vector3     $clickVector
	 * @param Player|null $player
	 * @param array       $returnedItems
	 *
	 * @return bool
	 */
	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []): bool {
		if(!$this->hasEye() && $player !== null && $item instanceof EnderEye){
			$this->getPosition()->getWorld()->setBlock($this->getPosition(), $this->setEye(true));
			$this->createPortal();
			return true;
		}

		return false;
	}

	public function getDrops(Item $item): array    {
		return [];
	}

	protected function createPortal() : void {
		$center = $this->searchCenter();
		if($center === null){
			return;
		}
		$world = $this->getPosition()->getWorld();

		for($x = -2; $x <= 2; $x++){
			for($z = -2; $z <= 2; $z++){
				if(($x === -2||$x === 2) && ($z === -2||$z === 2)) continue;
				if($x === -2||$x === 2||$z === -2||$z === 2){
					if(!self::checkFrame($world->getBlock($center->add($x, 0, $z)))){
						return;
					}
				}
			}
		}
		for($x = -1; $x <= 1; $x++){
			for($z = -1; $z <= 1; $z++){
				$position = $center->add($x, 0, $z);

				if(!$world->getBlock($position) instanceof Air){
					$world->useBreakOn($position);
				}
				$world->setBlock($position, DimensionBlocks::END_PORTAL());
			}
		}
	}

	protected function searchCenter(array $blocks = []) : ?Vector3 {
		$position = $this->getPosition();
		$world = $position->getWorld();
		for($x = -2; $x <= 2; $x++){
			if($x === 0) continue;
			$block = $world->getBlock($position->add($x, 0, 0));
			$iBlock = $world->getBlock($position->add($x * 2, 0, 0));
			if(self::checkFrame($block) && !in_array($block, $blocks)){
				$blocks[] = $block;
				if(abs($x) === 1 && self::checkFrame($iBlock))
					return $this->searchCenter($blocks);
				for($z = -4; $z <= 4; $z++){
					if($z === 0) continue;
					$block = $world->getBlock($position->add($x, 0, $z));
					if(self::checkFrame($block)){
						return $position->add($x / 2, 0, $z / 2);
					}
				}
			}
		}
		for($z = -2; $z <= 2; $z++){
			if($z === 0) {
                continue;
            }
			$block = $world->getBlock($position->add(0, 0, $z));
			$iBlock = $world->getBlock($position->add(0, 0, $z * 2));

			if(self::checkFrame($block) && !in_array($block, $blocks)) {
				$blocks[] = $block;
				if(abs($z) === 1 && self::checkFrame($iBlock))
					return $this->searchCenter($blocks);
				for($x = -4; $x <= 4; $x++){
					if($x === 0) continue;
					$block = $world->getBlock($position->add($x, 0, $z));
					if(self::checkFrame($block)){
						return $position->add($x / 2, 0, $z / 2);
					}
				}
			}
		}
		return null;
	}

	static protected function checkFrame(Block $block) : bool {
		return ($block instanceof EndPortalFrame && $block->hasEye());
	}
}