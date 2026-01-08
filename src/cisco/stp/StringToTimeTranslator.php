<?php

namespace cisco\stp;

use RuntimeException;

final class StringToTimeTranslator {

    private function __construct(){
    }

    public static function format(int $timestamp) : string {
        if ($timestamp > time()) {
            $timestamp -= time();
        }

        $d = floor($timestamp / 86400);
        $h = floor(($timestamp % 86400) / 3600);
        $m = floor(($timestamp % 3600) / 60);
        $s = $timestamp % 60;

        if($d > 0){
            return sprintf("%d days %02d:%02d:%02d", $d, $h, $m, $s);
        }elseif($h > 0){
            return sprintf("%02d:%02d:%02d", $h, $m, $s);
        }elseif($m > 0){
            return sprintf("%02d:%02d", $m, $s);
        }

        return sprintf("%ds", $s);
    }

    public static function convert(string $time): int {
        if (!preg_match('/^(\d+)([mhdsy])$/', $time, $matches)) {
            throw new RuntimeException('&r&cInvalid time format: ' . $time);
        }
        $value = (int)$matches[1];
        $unit = $matches[2];
        return self::toSeconds($value, $unit);
    }

    private static function toSeconds(int $value, string $unit): int {
        return match ($unit) {
            's' => $value,
            'm' => $value * 60,
            'h' => $value * 3600,
            'd' => $value * 86400,
            'y' => $value * 31536000,
            default => throw new RuntimeException('&r&cInvalid time unit: ' . $unit),
        };
    }
}