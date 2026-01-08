<?php

namespace cisco\stp;

use DaveRandom\CallbackValidator\ReturnType;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionProperty;

final class ReflectionHandler {

    /**
     * @var array<string, ReflectionProperty>
     */
    private static array $propertyCache = [];
    /**
     * @var array<string, ReflectionMethod>
     */
    private static array $methodCache = [];

    /**
     * @param string $className
     * @param string $propertyName
     * @return ReflectionProperty
     * @throws ReflectionException
     */
    private static function getCachedProperty(string $className, string $propertyName): ReflectionProperty {
        $key = "$className::$propertyName";
        if (!isset(self::$propertyCache[$key])) {
            $refClass = new ReflectionClass($className);
            $refProp = $refClass->getProperty($propertyName);
            $refProp->setAccessible(true);
            self::$propertyCache[$key] = $refProp;
        }
        return self::$propertyCache[$key];
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    private static function getCachedMethod(string $className, string $methodName): ReflectionMethod {
        $key = "$className::$methodName";
        if (!isset(self::$methodCache[$key])) {
            $refClass = new ReflectionClass($className);
            $refMeth = $refClass->getMethod($methodName);
            $refMeth->setAccessible(true);
            self::$methodCache[$key] = $refMeth;
        }
        return self::$methodCache[$key];
    }

    /**
     * @param string $className
     * @param object $instance
     * @param string $propertyName
     * @return mixed
     * @throws ReflectionException
     */
    public static function getProperty(string $className, object $instance, string $propertyName): mixed {
        return self::getCachedProperty($className, $propertyName)->getValue($instance);
    }

    /**
     * @param string $className
     * @param object $instance
     * @param string $propertyName
     * @param mixed $value
     * @return void
     * @throws ReflectionException
     */
    public static function setProperty(string $className, object $instance, string $propertyName, mixed $value): void {
        self::getCachedProperty($className, $propertyName)->setValue($instance, $value);
    }

    /**
     * @param string $className
     * @param object $instance
     * @param string $methodName
     * @param mixed ...$args
     * @return mixed
     * @throws ReflectionException
     */
    public static function invoke(string $className, object $instance, string $methodName, mixed ...$args): mixed {
        return self::getCachedMethod($className, $methodName)->invoke($instance, ...$args);
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param mixed ...$args
     * @return mixed
     * @throws ReflectionException
     */
    public static function invokeStatic(string $className, string $methodName, mixed ...$args): mixed {
        return self::getCachedMethod($className, $methodName)->invoke(null, ...$args);
    }

    /**
     * @return array{0: ReflectionProperty[], 1: ReflectionMethod[]}
     */
    public static function export(): array{
        return [
            "property" => self::$propertyCache,
            "method" => self::$methodCache
        ];
    }

}