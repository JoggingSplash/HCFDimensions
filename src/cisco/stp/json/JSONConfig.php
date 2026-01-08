<?php

namespace cisco\stp\json;

use pocketmine\plugin\Plugin;
use pocketmine\utils\ConfigLoadException;
use pocketmine\utils\Filesystem;
use pocketmine\utils\Utils as PMMPUtils;
use Symfony\Component\Filesystem\Path;
use pocketmine\utils\Config;

final class JSONConfig {

    /**
     * @param string $path
     * @return JSONConfig
     */
    static public function parse(string $path): JSONConfig {
        return new JSONConfig($path);
    }

    /**
     * @param string $file
     * @param Plugin $plugin
     * @return JSONConfig
     */
    static public function parseFrom(string $file, Plugin $plugin): JSONConfig{
        return self::parse($plugin->getDataFolder() . $file);
    }

    /** @var array */
    protected $contents;

    /** @var string */
    protected $path;

    /**
     * @param string $file
     */
    private function __construct(string $file){
        $this->path = $file;

        try {
            $this->load($file);
        }catch(\Throwable $exception){
            var_dump($exception->getMessage());
        }
    }

    /**
     * @param string $file
     * @return void
     * @throws \JsonException
     */
    private function load(string $file): void {
        if(!file_exists($file)){
            $this->contents = [];
            $this->save();
            return;
        }

        if(Path::getExtension($file, true) !== "json"){
            throw new \JsonException("JSONConfig got Non-JSON file at: $file");
        }

        try {
            $config = json_decode(file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);
        }catch(\JsonException $e){
            $config = [];
        }

        if(!is_array($config)){
            throw new ConfigLoadException("File $file has no valid JSON structure");
        }

        $this->contents = $config;
    }

    /**
     * @param \JsonSerializable[] $objects
     * @return $this
     */
    public function put(array $objects): JSONConfig{
        PMMPUtils::validateArrayValueType($objects, fn(\JsonSerializable $serializable) => 1);
        $this->setAll(array_map(fn(\JsonSerializable $serializable) => $serializable->jsonSerialize(), $objects));
        return $this;
    }

    /**
     * @param string $objectClass
     * @return array
     * @throws \JsonException
     */
    public function out(string $objectClass): array{
        PMMPUtils::testValidInstance($objectClass, JsonToObject::class);
        /** @var JsonToObject $objectClass */
        return array_map(
            fn(array $jsonData) => $objectClass::jsonDeserialize($jsonData),
            $this->all()
        );
    }

    /**
     * @param array $contents
     * @return $this
     */
    public function setAll(array $contents): JSONConfig    {
        $this->contents = $contents;
        return $this;
    }


    /**
     * @return array
     */
    public function all(): array{
        return $this->contents;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function mustHaveData(): array {
        if(empty($this->contents)){
            throw new \Exception("Got empty JSON at $this->path");
        }

        return $this->contents;
    }


    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed{
        return $this->contents[$key] ??= $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, mixed $value): JSONConfig{
        $this->contents[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function remove(string $key): JSONConfig    {
        unset($this->contents[$key]);
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool{
        return isset($this->contents[$key]);
    }

    /**
     * @return void
     */
    public function save(): void {
        try {
            Filesystem::safeFilePutContents(
                $this->path,
                json_encode($this->contents, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING | JSON_PRETTY_PRINT)
            );
        }catch (\Exception $exception){
            var_dump($exception->getMessage());
        }
    }

}