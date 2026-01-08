<?php

namespace cisco\stp\math;

use CortexPE\std\AABBUtils;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\math\AxisAlignedBB;
use pocketmine\world\Position;

class BalanceUtils {

    /**
     * @param float $costPerBlock
     * @param AxisAlignedBB|null $box
     * @param Position ...$positions
     * @return float
     */
    public static function calculateClaimCost(float $costPerBlock, ?AxisAlignedBB &$box = null, Position ...$positions): float {
        if (empty($positions)) {
            return 0.0;
        }

        $box = $box ?? AABBUtils::fromCoordinates(...$positions);
        $width = $box->maxX - $box->minX + 1;
        $height = $box->maxY - $box->minY + 1;
        $depth = $box->maxZ - $box->minZ + 1;
        $volume = $width * $height * $depth;
        return $volume * $costPerBlock;
    }

    /**
     * @param float $moneyPerDamage
     * @param Durable ...$items
     * @return float
     */
    public static function calculateRepairCost(float $moneyPerDamage = 1, Durable ...$items): float {
        return array_reduce($items, function (float $carry, Durable $item) use ($moneyPerDamage): float {
            return $carry + ($item->getDamage() * $moneyPerDamage);
        }, 0.0);
    }

    /**
     * @param Enchantment $enchantment
     * @param int $level
     * @param float $levelMultiplier
     * @return int
     */
    public static function calculateEnchantmentCost(Enchantment $enchantment, int $level, float $levelMultiplier = 2.0): int    {
        $baseCostByRarity = [
            Rarity::COMMON => 5,
            Rarity::UNCOMMON => 8,
            RARITY::RARE => 12,
            RARITY::MYTHIC => 18,
        ];

        $baseCost = $baseCostByRarity[$enchantment->getRarity()] ?? 5;

        return (int) round(pow($baseCost * pow($level, $levelMultiplier), 2));
    }

    /**
     * @param array $returnedItems
     * @param Item ...$ores
     * @return float
     */
    public static function calculateCostOfOres(array &$returnedItems = [], Item ...$ores): float    {
        if(empty($ores)){
            return 0.0;
        }

        static $orePrices = [
            ItemTypeIds::DIAMOND => 50.0,
            ItemTypeIds::RAW_IRON, ItemTypeIds::CHEMICAL_IRON_SULPHIDE => 20.0,
            ItemTypeIds::RAW_GOLD => 30.0,
            ItemTypeIds::COAL, ItemTypeIds::CHARCOAL, ItemTypeIds::CHEMICAL_CHARCOAL => 10.0,
            ItemTypeIds::REDSTONE_DUST => 15.0,
            ItemTypeIds::EMERALD => 60.0,
            ItemTypeIds::LAPIS_LAZULI => 12.0
        ];

        $totalCost = 0.0;

        foreach ($ores as $slot => $item) {
            $typeId = $item->getTypeId();
            $count = $item->getCount();

            if(isset($orePrices[$typeId])) {
                $returnedItems[$slot] = $item;
                $totalCost += ($orePrices[$typeId] * $count) / count($ores);
            }
        }

        return $totalCost;
    }

}