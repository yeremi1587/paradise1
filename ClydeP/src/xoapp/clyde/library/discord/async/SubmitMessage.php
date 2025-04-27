<?php

namespace xoapp\clyde\library\discord\async;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use xoapp\clyde\library\discord\Message;
use xoapp\clyde\library\discord\Webhook;

class SubmitMessage extends AsyncTask {

    private readonly string $url;
    private readonly mixed $message;
    private readonly bool $hasFile;

    public function __construct(
        Webhook $webhook,
        Message $message
    ) {
        $this->url = $webhook->getURL();
        $this->hasFile = $message->hasFile();

        if ($message->hasFile()) {
            $this->message = $message->jsonSerialize();
        } else {
            $this->message = json_encode($message);
        }
    }

    public function onRun(): void {
        $result = $this->send($this->url, $this->message, $this->hasFile);

        $this->setResult($result);
    }

    public function onCompletion(): void {
        $response = $this->getResult();

        if (in_array($response[1], [200, 204])) {
            return;
        }

        Server::getInstance()->getLogger()->error('[Discord] Got error (' . $response[1] . '): ' . $response[0]);
    }

    private function send(string $url, mixed $message, bool $hasFile): array {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 400);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);

        if (!$hasFile) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
        }

        $ret = [curl_exec($ch), curl_getinfo($ch, CURLINFO_RESPONSE_CODE)];

        curl_close($ch);

        return $ret;
    }
}