<?php

namespace cisco\dimensions\event;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;

class PlayerDimensionChangeEvent extends PlayerEvent implements Cancellable {
    use CancellableTrait;

    public function __construct(
        Player $player,
        readonly protected World $from,
        protected Position $to
    ){
        $this->player = $player;
    }

    /**
     * @return World
     */
    public function getFrom(): World
    {
        return $this->from;
    }

    /**
     * @return Position
     */
    public function getTo(): Position
    {
        return $this->to;
    }

    /**
     * @param Position $to
     */
    public function setTo(Position $to): void
    {
        $this->to = $to;
    }
}