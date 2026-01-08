<?php

namespace cisco\dimensions\world;

use cisco\dimensions\Loader;
use cisco\dimensions\world\generator\nether\NetherGenerator;
use cisco\dimensions\YAMLSettings;
use cisco\dimensions\world\generator\end\EndGenerator;
use cisco\stp\ReflectionHandler;
use muqsit\asynciterator\AsyncIterator;
use muqsit\asynciterator\handler\AsyncForeachResult;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\world\format\io\data\BaseNbtWorldData;
use pocketmine\world\format\io\LoadedChunkData;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;

final class WorldManager {

	protected static World $end;
	protected static World $nether;
	protected static World $default;

	public static function init() : void{
		$generator = GeneratorManager::getInstance();

		$remove = function() : void{
			unset($this->list["nether"]);// we dont want the actual nether generator
		};

		$remove->call($generator);

		$config = Loader::getInstance()->getConfig();
		$endName = $config->get(YAMLSettings::END_NAME, YAMLSettings::DEFAULT_END_NAME);
		$netherName = $config->get(YAMLSettings::NETHER_NAME, YAMLSettings::DEFAULT_NETHER_NAME);

		$generator->addGenerator(EndGenerator::class, "end", fn() => null);
		$generator->addGenerator(NetherGenerator::class, "nether", fn() => null);

		$worldManagerPM = Server::getInstance()->getWorldManager();
		if(!$worldManagerPM->loadWorld($endName)){
			$worldManagerPM->generateWorld($endName,
				WorldCreationOptions::create()
                    ->setGeneratorClass(EndGenerator::class)
                    ->setSpawnPosition(new Vector3(0, 70, 0))
			);
		}

		if(!$worldManagerPM->loadWorld($netherName)){
			$worldManagerPM->generateWorld($netherName,
				WorldCreationOptions::create()
                    ->setGeneratorClass(NetherGenerator::class)
                    ->setSpawnPosition(new Vector3(0, 70, 0))

            );
		}

		self::$end = $worldManagerPM->getWorldByName($endName) ?? throw new \RuntimeException("Unable to load and save world end: $endName");
		self::$nether = $worldManagerPM->getWorldByName($netherName) ?? throw new \RuntimeException("Unable to load and save world nether: $netherName");
    }

	/**
	 * @return World
	 */
	public static function END() : World{
		return self::$end;
	}

	/**
	 * @return World
	 */
	public static function NETHER() : World{
		return self::$nether;
	}

	/**
	 * @return World
	 */
	public static function DEFAULT() : World{
		return self::$default ??= Server::getInstance()->getWorldManager()->getDefaultWorld();
	}

}