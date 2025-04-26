
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\elements;

use pocketmine\form\FormValidationException;

class Dropdown extends Element {

    /** @var string[] */
    protected $options;
    /** @var int */
    protected $defaultOption;
    /** @var mixed */
    protected $value;

    /**
     * @param string $text
     * @param string[] $options
     * @param int $defaultOption
     */
    public function __construct(string $text, array $options, int $defaultOption = 0) {
        parent::__construct($text);
        $this->type = "dropdown";
        $this->options = $options;
        $this->defaultOption = $defaultOption;
    }

    /**
     * @return int
     */
    public function getDefaultOption() : int {
        return $this->defaultOption;
    }

    /**
     * @return string[]
     */
    public function getOptions() : array {
        return $this->options;
    }

    /**
     * Returns the value of the element
     * @return mixed
     */
    public function getValue() {
        return $this->value ?? $this->defaultOption;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value) : void {
        $this->value = $value;
    }

    /**
     * Validates the value returned from the form
     * @param mixed $value
     *
     * @throws FormValidationException if the value is invalid
     */
    public function validateValue($value) : void {
        if(!is_int($value) && !is_float($value) && !is_string($value)) {
            throw new FormValidationException("Expected int or string, got " . gettype($value));
        }

        // Convert to integer if it's a string representation of a number
        if(is_string($value) && is_numeric($value)) {
            $value = intval($value);
        }

        // If integer, verify it's a valid option
        if(is_int($value) && ($value < 0 || $value >= count($this->options))) {
            throw new FormValidationException("Option $value does not exist");
        }
    }

    /**
     * @return array
     */
    public function serializeElementData() : array {
        return [
            "type" => $this->type,
            "text" => $this->text,
            "options" => $this->options,
            "default" => $this->defaultOption
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array {
        return $this->serializeElementData();
    }
}
