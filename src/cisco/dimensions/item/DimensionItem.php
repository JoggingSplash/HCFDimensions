<?php

namespace cisco\dimensions\item;

use cisco\dimensions\item\preset\EnderEye;
use cisco\dimensions\item\preset\MilkBucket;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\utils\CloningRegistryTrait;

/**
 * @method static EnderEye ENDER_EYE()
 */
final class DimensionItem {
	use CloningRegistryTrait;

	private function __construct(){
	}

	protected static function register(string $name, Item $item) : void{
		self::_registryRegister($name, $item);
	}

	public static function getAll() : array{
		/** @var Item[] $result */
		$result = self::_registryGetAll();
		return $result;
	}

	protected static function setup() : void{
		self::register("ender_eye", new EnderEye(new ItemIdentifier(ItemTypeIds::newId()), "Eye of Ender"));
	}
}