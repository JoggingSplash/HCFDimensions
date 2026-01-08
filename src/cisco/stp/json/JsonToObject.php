<?php

namespace cisco\stp\json;

interface JsonToObject extends \JsonSerializable {


    /**
     * Returns an instance of this class, throws if data is missing
     * @param array $json
     * @return JsonToObject
     * @throws \JsonException
     */
    public static function jsonDeserialize(array $json): JsonToObject;

    /**
     * Serialize data of this item, must be array
     * @return array
     */
    public function jsonSerialize(): array;
}