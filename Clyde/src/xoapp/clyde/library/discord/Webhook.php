<?php

namespace xoapp\clyde\library\discord;

use pocketmine\Server;
use xoapp\clyde\library\discord\async\SubmitMessage;

final class Webhook {

    public function __construct(
        protected string $url
    ) {}

    public static function create(string $url): Webhook {
        return new Webhook($url);
    }

    public function isValid(): bool {
        return filter_var($this->url, FILTER_VALIDATE_URL) !== false;
    }

    public function getURL(): string {
        return $this->url;
    }

    public function send(Message $message): void {
        Server::getInstance()->getAsyncPool()->submitTask(new SubmitMessage($this, $message));
    }
}