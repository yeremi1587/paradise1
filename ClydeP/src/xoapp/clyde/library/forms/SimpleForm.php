<?php

namespace xoapp\clyde\library\forms;

class SimpleForm extends Form {

    private string $content = "";

    private array $labelMap = [];

    public function __construct(?callable $callable) {
        parent::__construct($callable);
        $this->data["type"] = "form";
        $this->data["title"] = "";
        $this->data["content"] = $this->content;
        $this->data["buttons"] = [];
    }

    public function processData(&$data): void {
        $data = $this->labelMap[$data] ?? null;
    }

    public function setTitle(string $title): void {
        $this->data["title"] = $title;
    }

    public function getTitle(): string {
        return $this->data["title"];
    }

    public function getContent(): string {
        return $this->data["content"];
    }

    public function setContent(string $content): void {
        $this->data["content"] = $content;
    }

    public function setButton(string $text, int $imageType = -1, string $imagePath = "", ?string $label = null): void {
        $content = ["text" => $text];

        if ($imageType !== -1) {
            $content["image"]["type"] = $imageType === 0 ? "path" : "url";
            $content["image"]["data"] = $imagePath;
        }

        $this->data["buttons"][] = $content;
        $this->labelMap[] = $label ?? count($this->labelMap);
    }
}