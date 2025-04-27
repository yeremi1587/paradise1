<?php

namespace xoapp\clyde\library\forms;

use pocketmine\form\Form as IForm;
use pocketmine\player\Player;

abstract class Form implements IForm {

    protected array $data = [];

    private $callable;

    public function __construct(?callable $callable) {
        $this->callable = $callable;
    }

    public function getCallable(): ?callable {
        return $this->callable;
    }

    public function setCallable(?callable $callable): void {
        $this->callable = $callable;
    }

    public function handleResponse(Player $player, $data): void {
        $this->processData($data);
        $callable = $this->getCallable();
        if (!is_null($callable)) {
            $callable($player, $data);
        }
    }

    public function processData(&$data): void {}

    public function jsonSerialize(): array {
        return $this->data;
    }
}