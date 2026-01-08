<?php

namespace cisco\stp;

use Closure;
use pocketmine\scheduler\Task;
use pocketmine\utils\Utils;

final class ClosureCancelableTask extends Task {

    public function __construct(
        protected \Closure $closure,
        protected \Closure $condition,
        protected ?Closure $onCancel = null
    ){
        Utils::validateCallableSignature($this->closure, function(): void {});
        Utils::validateCallableSignature($this->condition, fn(): bool => true);

        if($this->onCancel !== null){
            Utils::validateCallableSignature($this->onCancel, function(): void {});
        }
    }

    public function onRun(): void {
		if(($this->condition)()){
			$this->getHandler()?->cancel();
			return;
		}

		($this->closure)();
    }

    public function onCancel() : void{
        if($this->onCancel !== null){
            ($this->onCancel)();
        }
    }
}