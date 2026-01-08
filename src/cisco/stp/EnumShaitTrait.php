<?php

namespace cisco\stp;

trait EnumShaitTrait {

    /**
     * @param string|int $value
     */
    abstract public static function fromValue(string|int $value): ?self;

    /**
     * @return string|int|null
     */
    public function getValue(): string|int|null {
        return $this->value;
    }

    /**
     * @return string
     */
    public function toString(): string{
        return (string)$this->value ?? "";
    }

    /**
     * @param EnumShaitTrait $other
     * @return bool
     * @deprecated
     */
    public function equals(self $other): bool {
        return $this === $other;
    }

}