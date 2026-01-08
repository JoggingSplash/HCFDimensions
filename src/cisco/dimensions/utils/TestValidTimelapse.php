<?php

namespace cisco\dimensions\utils;

use pocketmine\block\Block;
use pocketmine\player\Player;
use pocketmine\world\format\Chunk;
use pocketmine\world\Position;

/**
 * Since {@link Block::onEntityInside()} is called multiple times in a row
 * if we dont use a system based on timelapse, the player is going to teleport a lot of times
 */

final class TestValidTimelapse {
	protected static array $timestamps = [];

    private static Limiter $limiter;

    /**
     * @param Player $player
     * @param Position $position
     * @return bool
     */
	public static function test(Player $player, Position $position): bool {
        return self::limiter()->response($player->getId(), $player, $position);
    }

    static private function limiter(): Limiter    {
        // 2 s is fine, I think...
        return self::$limiter ??= new Limiter(2.0, function(Player $player, Position $position): void {
            $position->getWorld()->loadChunk(
                $position->getFloorX() >> Chunk::COORD_BIT_SIZE,
                $position->getFloorZ() >> Chunk::COORD_BIT_SIZE
            );
            $player->teleport($position);
        });
    }
}