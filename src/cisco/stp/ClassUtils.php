<?php

namespace cisco\stp;

use Closure;
use pocketmine\math\Vector3;
use pocketmine\utils\Utils;
use pocketmine\world\World;

class ClassUtils {

    /**
     * @param Closure $callback
     * @param object $obj
     * @return void
     */
    public static function callbackOnObject(\Closure $callback, object $obj): void {
        Utils::validateCallableSignature($callback, fn() => 0);
        $callback->call($obj);
    }

    /**
     * @param string $class
     * @param string $method
     * @param array $argc
     * @return array
     */
    public static function silentMethod(string $class, string $method, array $argc): array    {
        return array_map([$class, $method], $argc);
    }


}