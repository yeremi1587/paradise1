<?php

namespace xoapp\clyde\library\forms;

class CustomForm extends Form {

    private array $labelMap = [];

    public function __construct(?callable $callable) {
        parent::__construct($callable);
        $this->data["type"] = "custom_form";
        $this->data["title"] = "";
        $this->data["content"] = [];
    }

    public function processData(&$data): void {
        if (is_array($data)) {
            $new = [];
            foreach ($data as $i => $v) {
                $new[$this->labelMap[$i]] = $v;
            }
            $data = $new;
        }
    }

    public function setTitle(string $title): void {
        $this->data["title"] = $title;
    }

    public function getTitle(): string {
        return $this->data["title"];
    }

    public function setLabel(string $text, ?string $label = null): void {
        $this->addContent(["type" => "label", "text" => $text]);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function setToggle(string $text, bool $default = null, ?string $label = null): void {
        $content = ["type" => "toggle", "text" => $text];
        if ($default !== null) {
            $content["default"] = $default;
        }
        $this->addContent($content);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function setSlider(string $text, int $min, int $max, int $step = -1, int $default = -1, ?string $label = null): void {
        $content = ["type" => "slider", "text" => $text, "min" => $min, "max" => $max];
        if ($step !== -1) {
            $content["step"] = $step;
        }
        if ($default !== -1) {
            $content["default"] = $default;
        }
        $this->addContent($content);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function setStepSlider(string $text, array $steps, int $defaultIndex = -1, ?string $label = null): void {
        $content = ["type" => "step_slider", "text" => $text, "steps" => $steps];
        if ($defaultIndex !== -1) {
            $content["default"] = $defaultIndex;
        }
        $this->addContent($content);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function setDropdown(string $text, array $options, int $default = null, ?string $label = null): void {
        $this->addContent(["type" => "dropdown", "text" => $text, "options" => $options, "default" => $default]);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function setInput(string $text, string $placeholder = "", string $default = null, ?string $label = null): void {
        $this->addContent(["type" => "input", "text" => $text, "placeholder" => $placeholder, "default" => $default]);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    private function addContent(array $content): void {
        $this->data["content"][] = $content;
    }
}