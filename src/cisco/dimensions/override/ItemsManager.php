<?php

namespace cisco\dimensions\override;

use bitpvp\hcf\item\NonVanillaItems;
use cisco\dimensions\blocks\DimensionBlocks;
use cisco\dimensions\blocks\preset\EndPortalFrame;
use cisco\dimensions\blocks\preset\NetherPortal;
use cisco\dimensions\item\DimensionItem;
use cisco\dimensions\item\preset\EnderEye;
use cisco\stp\BetterSingletonTrait;
use Closure;
use pocketmine\block\Block;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\data\bedrock\block\BlockStateNames;
use pocketmine\data\bedrock\block\BlockStateSerializeException;
use pocketmine\data\bedrock\block\BlockStateStringValues;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\data\bedrock\item\ItemTypeDeserializeException;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\item\Item;
use pocketmine\item\SplashPotion;
use pocketmine\item\StringToItemParser;
use pocketmine\math\Axis;
use pocketmine\Server;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use pocketmine\world\format\io\GlobalItemDataHandlers;

final class ItemsManager {
	use BetterSingletonTrait;

	public function init(): void    {
		$pool = Server::getInstance()->getAsyncPool(); //Using default async pool because of overrides :c
		$this->registerOnCurrentThread();
		$pool->addWorkerStartHook(function (int $workers) use ($pool): void {
			$pool->submitTaskToWorker(new ItemsAsync() ,$workers);
		});
	}

	public function registerOnCurrentThread(): void {
		$this->initItems();
		$this->initBlocks();
	}

	private function initItems(): void    {
		$this->setUpItem(ItemTypeNames::ENDER_EYE, DimensionItem::ENDER_EYE(), ["ender_eye"], function (Item $item): SavedItemData {
			assert($item instanceof EnderEye);
			return new SavedItemData(ItemTypeNames::ENDER_EYE);
		},
			function (SavedItemData $data) {
				return DimensionItem::ENDER_EYE(); //jst return it, does not matter ;v
			});
    }

	private function initBlocks(): void    {
		$this->setUpSimpleBlock(BlockTypeNames::END_PORTAL, DimensionBlocks::END_PORTAL(), ["end_portal"]);

		$this->setUpBlock(BlockTypeNames::OBSIDIAN, DimensionBlocks::OBSIDIAN(), ["obsidian"]);

		$nether = DimensionBlocks::NETHER_PORTAL();
		$this->setUpBlock(
			BlockTypeNames::PORTAL,
			$nether, ['nether_portal'],
			function (NetherPortal $portal): BlockStateWriter {
				$result = BlockStateWriter::create(BlockTypeNames::PORTAL);
				$result->writeString(BlockStateNames::PORTAL_AXIS, match ($portal->getAxis()) {
					Axis::X => BlockStateStringValues::PORTAL_AXIS_X,
					Axis::Z => BlockStateStringValues::PORTAL_AXIS_Z,
					default => throw new BlockStateSerializeException("Invalid Nether Portal axis " . $portal->getAxis()),
				});
				return $result;
			},
			function (BlockStateReader $state) use ($nether): Block {
				$result = clone $nether;
				$result->setAxis(match ($value = $state->readString(BlockStateNames::PORTAL_AXIS)) {
					BlockStateStringValues::PORTAL_AXIS_UNKNOWN, BlockStateStringValues::PORTAL_AXIS_X => Axis::X,
					BlockStateStringValues::PORTAL_AXIS_Z => Axis::Z,
					default => throw $state->badValueException(BlockStateNames::PORTAL_AXIS, $value),
				});
				return $result;
			});

		$end = DimensionBlocks::END_PORTAL_FRAME();
		$this->setUpBlock(
			BlockTypeNames::END_PORTAL_FRAME,
			$end, ["end_portal_frame"],
			function (EndPortalFrame $frame): BlockStateWriter {
				$result = BlockStateWriter::create(BlockTypeNames::END_PORTAL_FRAME);
				$result->writeBool(BlockStateNames::END_PORTAL_EYE_BIT, $frame->hasEye());
				$result->writeCardinalHorizontalFacing($frame->getFacing());
				return $result;
			},
			function (BlockStateReader $state) use ($end): Block {
				$result = clone $end;
				$result->setEye($state->readBool(BlockStateNames::END_PORTAL_EYE_BIT));
				$result->setFacing($state->readCardinalHorizontalFacing());
				return $result;
			}
		);
	}


	private function setUpItem(string $id, Item $item, array $stringToItemParserNames, ?Closure $serializerCallback = null, ?Closure $deserializerCallback = null): void {
		$serializer = GlobalItemDataHandlers::getSerializer();
		$deserializer = GlobalItemDataHandlers::getDeserializer();

		$serializerFunction = function () use ($id, $item, $serializerCallback): void {
			$callback = $serializerCallback ?? fn() => new SavedItemData($id);
			$this->itemSerializers[$item->getTypeId()] = $callback;
		};

		$serializerFunction->call($serializer);

		//now we can override deserializer only
		$callback = $deserializerCallback ?? fn(SavedItemData $_) => clone $item;
		$deserializer->map($id, $callback);

		foreach ($stringToItemParserNames as $name) {
			StringToItemParser::getInstance()->override($name, fn() => clone $item);
		}
	}

	private function setUpBlock(string $id, Block $block, array $stringToItemParserNames, ?Closure $serializerCallback = null, ?Closure $deserializerCallback = null) : void{
		$deserializer = GlobalBlockStateHandlers::getDeserializer();
		$serializer = GlobalBlockStateHandlers::getSerializer();

		$serializerFunction = function () use ($id, $block, $serializerCallback): void {
			if(isset($this->serializers[$id])){
				unset($this->serializers[$id]);
			}

			$this->serializers[$id] = $serializerCallback ?? fn() => new BlockStateWriter($id);
		};

		$serializerFunction->call($serializer);

		//now we can override only deserializer
		$callBack = $deserializerCallback ?? fn() => clone $block;

		$deserializer->map($id, $callBack);

		$registrant = RuntimeBlockStateRegistry::getInstance();

		$registrantFunction = function() use ($block): void {
			if(isset($this->typeIndex[$block->getTypeId()])){
				unset($this->typeIndex[$block->getTypeId()]);
			}
			$this->register($block);
		};

		$registrantFunction->call($registrant);

		foreach($stringToItemParserNames as $name){
			StringToItemParser::getInstance()->override($name, fn() => clone $block->asItem());
		}
	}

	private function setUpSimpleBlock(string $id, Block $block, array $stringToItemParserNames): void    {
		RuntimeBlockStateRegistry::getInstance()->register($block);

		GlobalBlockStateHandlers::getDeserializer()->mapSimple($id, fn() => clone $block);
		GlobalBlockStateHandlers::getSerializer()->mapSimple($block, $id);

		foreach($stringToItemParserNames as $name){
			StringToItemParser::getInstance()->override($name, fn() => clone $block->asItem());
		}
	}
}