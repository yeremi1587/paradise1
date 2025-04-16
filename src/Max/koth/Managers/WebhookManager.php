
<?php

declare(strict_types=1);

namespace Max\koth\Managers;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use Max\koth\KOTH;

class WebhookManager {
    private KOTH $plugin;

    public function __construct(KOTH $plugin) {
        $this->plugin = $plugin;
    }

    public function sendKothStartMessage(string $arenaName, string $coordinates): void {
        if (!$this->plugin->config->USE_WEBHOOK) {
            return;
        }

        $webhook = new Webhook($this->plugin->config->WEBHOOK_URL);
        $msg = new Message();
        $embed = new Embed();
        $embed->setTitle("KOTH Started");
        $embed->setDescription("A new KOTH event has started at arena " . $arenaName . "\nCoordinates: " . $coordinates);
        $embed->setColor(0x00ff00);
        $msg->addEmbed($embed);
        $webhook->send($msg);
    }

    public function sendKothEndMessage(string $winnerName): void {
        if (!$this->plugin->config->USE_WEBHOOK) {
            return;
        }

        $webhook = new Webhook($this->plugin->config->WEBHOOK_URL);
        $msg = new Message();
        $embed = new Embed();
        $embed->setTitle("KOTH Ended");
        $embed->setDescription("The KOTH event has ended. Winner: " . $winnerName);
        $embed->setColor(0xff0000);
        $msg->addEmbed($embed);
        $webhook->send($msg);
    }
}
