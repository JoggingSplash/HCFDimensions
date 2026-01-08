<?php

namespace cisco\stp;

use pocketmine\utils\SingletonTrait;

/**
 * {@link SingletonTrait}
 */
trait BetterSingletonTrait {

    private static self $instance;

    /**
     * TODO: debugging, SingletonTrait use more performance that this
     * this is because we are already setting the value and we dont need to ask if its null
     * {@link SingletonTrait::getInstance()}
     */
    public static function getInstance(): self {
        return self::$instance ??= self::make();
    }

    protected static function make(): self    {
        return new self();
    }

    protected static function setInstance(self $instance): void {
        self::$instance = $instance;
    }
}