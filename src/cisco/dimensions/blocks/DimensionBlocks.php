<?php

namespace cisco\dimensions\blocks;

use cisco\dimensions\blocks\preset\Cobweb;
use cisco\dimensions\blocks\preset\DragonEgg;
use cisco\dimensions\blocks\preset\EndPortal;
use cisco\dimensions\blocks\preset\EndPortalFrame;
use cisco\dimensions\blocks\preset\NetherPortal;
use cisco\dimensions\blocks\preset\Obsidian;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockBreakInfo as BreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\block\BlockTypeInfo as Info;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ToolTier;
use pocketmine\utils\CloningRegistryTrait;

/**
 * @method static EndPortalFrame END_PORTAL_FRAME()
 * @method static NetherPortal NETHER_PORTAL()
 * @method static Obsidian OBSIDIAN()
 * @method static EndPortal END_PORTAL()
 */

final class DimensionBlocks {
	use CloningRegistryTrait;

	protected static function register(string $name, Block $block) : void{
		self::_registryRegister($name, $block);
	}

	public static function getAll() : array{
		/** @var Block[] $result */
		$result = self::_registryGetAll();
		return $result;
	}

	protected static function setup(): void    {
		self::register("end_portal_frame", new EndPortalFrame(new BlockIdentifier(BlockTypeIds::END_PORTAL_FRAME), "End Portal Frame", new BlockTypeInfo(BlockBreakInfo::indestructible())));
		self::register("nether_portal", new NetherPortal(new BlockIdentifier(BlockTypeIds::NETHER_PORTAL), "Nether Portal", new BlockTypeInfo(BlockBreakInfo::indestructible())));
		self::register("obsidian", new Obsidian(new BlockIdentifier(BlockTypeIds::OBSIDIAN), "Obsidian", new BlockTypeInfo(clone VanillaBlocks::OBSIDIAN()->getBreakInfo())));
		self::register("end_portal", new EndPortal(new BlockIdentifier(BlockTypeIds::newId()), "End Portal", new BlockTypeInfo(BlockBreakInfo::indestructible())));
	}
}