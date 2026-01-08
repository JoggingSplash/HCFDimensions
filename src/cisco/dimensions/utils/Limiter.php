<?php

namespace cisco\dimensions\utils;

final class Limiter {

    /**
     * @var array<int, float> $blocks
     */
    private array $blocks = [];

    public function __construct(
        private float $delay,
        private \Closure $callback
    ){

    }

    /**
     * @param int $index
     * @param mixed ...$args
     * @return bool
     */
    public function response(int $index, mixed ...$args): bool    {
        if(($this->blocks[$index] ??= 0.0) >= microtime(true)){
            return false;
        }

        ($this->callback)(...$args);
        $this->blocks[$index] = microtime(true) + $this->delay;
        return true;
    }

}