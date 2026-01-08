<?php

namespace cisco\stp\json;

class JsonDataMissing extends \JsonException {

    public function __construct(string $key){
        parent::__construct("Expected to got value non-null at $key in JsonConfig");
    }

    /**
     * @throws \JsonException
     */
    static public function parse(string $key, array $json): mixed {
        if(!isset($json[$key])){
            throw new self($key);
        }

        return $json[$key];
    }
}