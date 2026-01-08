<?php

namespace cisco\stp;

use pocketmine\player\Player;

trait CombatTrait {


    /**
     * @var array{0: int, 1: ?Player}
     */
    private array $cooldown = [];

    /**
     * @return int
     */
    public function getCooldown(): int {
        return $this->cooldown[0] ??= 0;
    }

    /**
     * @return Player|null
     */
    public function getPlayerInCombat(): ?Player {
        return $this->cooldown[1] ??= null;
    }

    /**
     * @param int $cooldown
     * @param Player|null $player
     * @return void
     */
    public function setCooldown(int $cooldown, ?Player $player = null): void {
        $this->cooldown[0] = $cooldown;
        $this->cooldown[1] = $player;
    }
}