
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\elements;

use pocketmine\form\FormValidationException;

class Toggle extends Element {
    /** @var bool */
    protected $default;

    /**
     * @param string $text
     * @param bool $default
     */
    public function __construct(string $text, bool $default = false) {
        parent::__construct($text);
        $this->type = "toggle";
        $this->default = $default;
    }

    /**
     * @return bool
     */
    public function getDefault(): bool {
        return $this->default;
    }

    /**
     * @param mixed $value
     * @throws FormValidationException
     */
    public function validateValue($value): void {
        if(!is_bool($value)) {
            throw new FormValidationException("Expected bool, got " . gettype($value));
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
            "default" => $this->default
        ];
    }
}
