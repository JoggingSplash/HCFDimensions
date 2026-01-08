<?php


namespace cisco\dimensions;
use cisco\dimensions\override\ItemsManager;
use cisco\dimensions\world\WorldManager;
use cisco\stp\BetterSingletonTrait;
use pocketmine\command\ClosureCommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

final class Loader extends PluginBase {
	use BetterSingletonTrait;


	protected function onLoad() : void{
		self::setInstance($this);
		$this->saveDefaultConfig();
		WorldManager::init();
	}

	protected function onEnable() : void{
		ItemsManager::getInstance()->init();

        $this->getServer()->getCommandMap()->register("test", new ClosureCommand(
            "world",
            function(CommandSender $sender, Command $command, string $commandLabel, array $args): mixed {
                if(!$sender instanceof Player){
                    return 0;
                }

                if(!isset($args[0])){
                    return 0;
                }

                $name = $args[0];
                $wm = $sender->getServer()->getWorldManager();

                if(!$wm->isWorldLoaded($name)){
                    $wm->loadWorld($name);
                }

                $world = $wm->getWorldByName($name);

                if($world === null){
                    return 0;
                }

                $sender->teleport($world->getSpawnLocation());
                return 1;
            },
            [DefaultPermissions::ROOT_OPERATOR]
        ));
    }
}