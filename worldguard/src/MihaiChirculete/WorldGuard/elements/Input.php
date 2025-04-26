
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\elements;

use pocketmine\form\FormValidationException;

class Input extends Element {
    /** @var string */
    protected $placeholder;
    /** @var string */
    protected $default;

    /**
     * @param string $text
     * @param string $placeholder
     * @param string $default
     */
    public function __construct(string $text, string $placeholder = "", string $default = "") {
        parent::__construct($text);
        $this->type = "input";
        $this->placeholder = $placeholder;
        $this->default = $default;
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string {
        return $this->placeholder;
    }

    /**
     * @return string
     */
    public function getDefault(): string {
        return $this->default;
    }

    /**
     * @param mixed $value
     * @throws FormValidationException
     */
    public function validateValue($value): void {
        if(!is_string($value)) {
            throw new FormValidationException("Expected string, got " . gettype($value));
        }
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void {
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function serializeElementData(): array {
        return [
            "type" => $this->type,
            "text" => $this->text,
            "placeholder" => $this->placeholder,
            "default" => $this->default
        ];
    }
}
