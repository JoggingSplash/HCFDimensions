<?php

namespace cisco\stp\sound;

enum Sound: string {
    // Random
    case RANDOM_CLICK = 'random.click';
    case RANDOM_BOW = 'random.bow';
    case RANDOM_BREAK = 'random.break';
    case RANDOM_GLASS = 'random.glass';
    case RANDOM_ORB = 'random.orb';
    case RANDOM_POP = 'random.pop';
    case RANDOM_EXPLODE = 'random.explode';
    case RANDOM_ANVIL_LAND = 'random.anvil_land';
    case RANDOM_ANVIL_BREAK = 'random.anvil_break';
    case RANDOM_ANVIL_USE = 'random.anvil_use';

    // Step
    case STEP_GRASS = 'step.grass';
    case STEP_STONE = 'step.stone';
    case STEP_WOOD = 'step.wood';
    case STEP_SAND = 'step.sand';
    case STEP_GRAVEL = 'step.gravel';
    case STEP_SNOW = 'step.snow';
    case STEP_LADDER = 'step.ladder';

    // Dig
    case DIG_WOOD = 'dig.wood';
    case DIG_GRASS = 'dig.grass';
    case DIG_STONE = 'dig.stone';
    case DIG_SAND = 'dig.sand';
    case DIG_SNOW = 'dig.snow';
    case DIG_GRAVEL = 'dig.gravel';

    // Mob
    case MOB_PIG_SAY = 'mob.pig.say';
    case MOB_COW_SAY = 'mob.cow.say';
    case MOB_SHEEP_SAY = 'mob.sheep.say';
    case MOB_CHICKEN_SAY = 'mob.chicken.say';
    case MOB_ZOMBIE_SAY = 'mob.zombie.say';
    case MOB_SKELETON_SAY = 'mob.skeleton.say';
    case MOB_CREEPER_DEATH = 'mob.creeper.death';
    case MOB_CREEPER_PRIMED = 'creeper.primed';
    case MOB_ENDERMAN_SCREAM = 'mob.enderman.scream';
    case MOB_WOLF_BARK = 'mob.wolf.bark';
    case MOB_WOLF_HOWL = 'mob.wolf.howl';
    case MOB_BLAZE_BREATHE = 'mob.blaze.breathe';
    case MOB_SLIME_ATTACK = 'mob.slime.attack';
    case MOB_SLIME_BIG = 'mob.slime.big';
    case MOB_SLIME_SMALL = 'mob.slime.small';
    case MOB_GHAST_SCREAM = 'mob.ghast.scream';
    case MOB_GHAST_DEATH = 'mob.ghast.death';
    case MOB_VILLAGER_IDLE = 'mob.villager.idle';
    case MOB_VILLAGER_NO = 'mob.villager.no';
    case MOB_VILLAGER_YES = 'mob.villager.yes';

    // Portal
    case PORTAL_TRIGGER = 'portal.trigger';
    case PORTAL_TRAVEL = 'portal.travel';

    // Ambient
    case AMBIENT_CAVE = 'ambient.cave.cave';
    case AMBIENT_THUNDER = 'ambient.weather.thunder';
    case AMBIENT_RAIN = 'ambient.weather.rain';

    // Player
    case PLAYER_HURT = 'game.player.hurt';
    case PLAYER_DIE = 'game.player.die';
    case PLAYER_SWIM = 'game.player.swim';
    case PLAYER_SPLASH = 'game.player.swim.splash';

    // Fire & Lava
    case FIRE_FIRE = 'fire.fire';
    case FIRE_IGNITE = 'fire.ignite';
    case LIQUID_LAVAPOP = 'liquid.lavapop';

    // Note Blocks
    case NOTE_BASS = 'note.bass';
    case NOTE_SNARE = 'note.snare';
    case NOTE_HAT = 'note.hat';
    case NOTE_PLING = 'note.pling';

    // Extra
    case ENDERMITE = "mob.endermite.ambient";

    public function getName(): string {
        return $this->value;
    }
}
