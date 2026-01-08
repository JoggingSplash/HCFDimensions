<?php

namespace cisco\dimensions\blocks\preset;

use cisco\dimensions\blocks\DimensionBlocks;
use pocketmine\block\Air;
use pocketmine\block\GlowingObsidian;
use pocketmine\item\Durable;
use pocketmine\item\FlintSteel;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class Obsidian extends GlowingObsidian {

	public function onBreak(Item $item, ?Player $player = null, array &$returnedItems = []): bool {
		foreach (Facing::ALL as $face) {
			if (($block = $this->getSide($face)) instanceof NetherPortal) {
				$block->onBreak($item, $player);
			}
		}
		return parent::onBreak($item, $player);
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []): bool {
		if ($player === null) {
			return false;
		}

		if (
			$player->getWorld() !== $player->getServer()->getWorldManager()->getDefaultWorld() &&
			!$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)
		) {
			return false;
		}

		if (!$item instanceof FlintSteel) {
			return false;
		}

		if ($this->tryCreatePortal($player, $item, true)) {
			return true;
		}

		return $this->tryCreatePortal($player, $item, false);
	}

	/**
	 * Intenta crear un portal en el eje especificado.
	 *
	 * @param Player $player
	 * @param Item   $item
	 * @param bool   $axisX  true para eje X, false para eje Z
	 * @return bool
	 */
	private function tryCreatePortal(Player $player, Item $item, bool $axisX): bool {
		$pos = $this->getPosition();
		$world = $pos->getWorld();

		if ($axisX) {
			$max = $min = $pos->x;
			for ($x = $pos->x + 1; $world->getBlockAt($x, $pos->y, $pos->z) instanceof self; $x++) $max++;
			for ($x = $pos->x - 1; $world->getBlockAt($x, $pos->y, $pos->z) instanceof self; $x--) $min--;
		} else {
			$max = $min = $pos->z;
			for ($z = $pos->z + 1; $world->getBlockAt($pos->x, $pos->y, $z) instanceof self; $z++) $max++;
			for ($z = $pos->z - 1; $world->getBlockAt($pos->x, $pos->y, $z) instanceof self; $z--) $min--;
		}

		$count = $max - $min + 1;
		if ($count < 4 || $count > 7) {
			return false;
		}

		if ($axisX) {
			$maxY = $minY = $pos->y;
			for ($y = $pos->y; $world->getBlockAt($max, $y, $pos->z) instanceof self; $y++) $maxY++;
			for ($y = $pos->y; $world->getBlockAt($min, $y, $pos->z) instanceof self; $y++) $minY++;
		} else {
			$maxY = $minY = $pos->y;
			for ($y = $pos->y; $world->getBlockAt($pos->x, $y, $max) instanceof self; $y++) $maxY++;
			for ($y = $pos->y; $world->getBlockAt($pos->x, $y, $min) instanceof self; $y++) $minY++;
		}

		$y_max = min($maxY, $minY) - 1;
		$count_y = $y_max - $pos->y + 2;

		if ($count_y < 5 || $count_y > 23) {
			return false;
		}

		$count_up = 0;
		if ($axisX) {
			for ($u = $min; $world->getBlockAt($u, $y_max, $pos->z) instanceof self && $u <= $max; $u++) $count_up++;
		} else {
			for ($u = $min; $world->getBlockAt($pos->x, $y_max, $u) instanceof self && $u <= $max; $u++) $count_up++;
		}

		if ($count_up !== $count) {
			return false;
		}

		if ($axisX) {
			for ($px = $min + 1; $px < $max; $px++) {
				for ($py = $pos->y + 1; $py < $y_max; $py++) {
					if ($world->getBlockAt($px, $py, $pos->z) instanceof Air) {
						$world->setBlockAt($px, $py, $pos->z, DimensionBlocks::NETHER_PORTAL());
					}
				}
			}
		} else {
			for ($pz = $min + 1; $pz < $max; $pz++) {
				for ($py = $pos->y + 1; $py < $y_max; $py++) {
					if ($world->getBlockAt($pos->x, $py, $pz) instanceof Air) {
						$world->setBlockAt($pos->x, $py, $pz, DimensionBlocks::NETHER_PORTAL());
					}
				}
			}
		}

		if ($player->isSurvival() && $item instanceof Durable) {
			$newItem = clone $item;
			$newItem->applyDamage(1);
			$player->getInventory()->setItemInHand($newItem);
		}

		return true;
	}
}
