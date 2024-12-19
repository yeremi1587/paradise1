<?php

declare(strict_types=1);

namespace Max\koth\Config;

class KothConfig {
    public int $TASK_DELAY = 20;
    public int $CAPTURE_TIME = 300;
    public bool $USE_BOSSBAR = true;
    public string $COLOR_BOSSBAR = "blue";
    public bool $SEND_TIPS = true;
    public bool $SEND_ACTIONBAR = true;
    public bool $USE_WEBHOOK = false;
    public string $WEBHOOK_URL = "";
    public array $REWARDS = [];
    public array $START_TIMES = [];
    public int $MIN_FACTION_POWER = 100;
    public bool $REQUIRE_FACTION = true;

    public function __construct(array $config) {
        $this->TASK_DELAY = $config["task-delay"] ?? 20;
        $this->CAPTURE_TIME = $config["capture-time"] ?? 300;
        $this->USE_BOSSBAR = $config["use-bossbar"] ?? true;
        $this->COLOR_BOSSBAR = $config["color-bossbar"] ?? "blue";
        $this->SEND_TIPS = $config["send-tips"] ?? true;
        $this->SEND_ACTIONBAR = $config["send-actionbar"] ?? true;
        $this->USE_WEBHOOK = $config["use-webhook"] ?? false;
        $this->WEBHOOK_URL = $config["webhook-url"] ?? "";
        $this->REWARDS = $config["rewards"] ?? [];
        $this->START_TIMES = $config["start-times"] ?? [];
        $this->MIN_FACTION_POWER = $config["min-faction-power"] ?? 100;
        $this->REQUIRE_FACTION = $config["require-faction"] ?? true;
    }
}