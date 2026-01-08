<?php

namespace cisco\dimensions\override;

use pocketmine\scheduler\AsyncTask;

class ItemsAsync extends AsyncTask{

	public function onRun() : void{
		ItemsManager::getInstance()->registerOnCurrentThread();
	}
}