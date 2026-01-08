<?php

namespace cisco\stp\sound;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\world\Position;

final class SoundUtils {

    private function __construct() {}

    static public function play(Player $player, Sound $sound, ?Position $position = null, float $pitch = 1.0, float $volume = 1.0): void{
        if(!$player->isOnline()){
            throw new \LogicException("Player is not online");
        }

        $pos = $position ?? $player->getPosition();
        $player->getNetworkSession()->sendDataPacket(PlaySoundPacket::create(
            $sound->getName(),
            $pos->x,
            $pos->y,
            $pos->z,
            $volume,
            $pitch
        ));
    }
}