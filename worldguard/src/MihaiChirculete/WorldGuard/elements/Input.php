
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\elements;

use pocketmine\form\FormValidationException;
use function is_string;

class Input extends Element
{
    /** @var string */
    private $placeholder;
    /** @var string */
    private $default;

    /**
     * @param string $text
     * @param string $placeholder
     * @param string $default
     */
    public function __construct(string $text, string $placeholder, string $default = "")
    {
        parent::__construct($text);
        $this->placeholder = $placeholder;
        $this->default = $default;
        $this->value = $default; // Initialize value with default to avoid null
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        $value = parent::getValue();
        
        // Handle null by returning default
        if ($value === null) {
            return $this->default;
        }
        
        // Ensure we always return a string, converting other types if necessary
        if (is_bool($value)) {
            return $value ? "true" : "false";
        } elseif (is_int($value) || is_float($value)) {
            return (string)$value;
        }
        
        return (string)$value;
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @return string
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return "input";
    }

    /**
     * @return array
     */
    public function serializeElementData(): array
    {
        return [
            "type" => $this->getType(),
            "text" => $this->getText(),
            "placeholder" => $this->placeholder,
            "default" => $this->default
        ];
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @param mixed $value
     * @throws FormValidationException if the value is invalid
     */
    public function validateValue($value): void
    {
        // Input values can be any type, but we'll convert to string later
        // No need to throw validation error for inputs unless it's something we can't convert
        if ($value !== null && !is_scalar($value) && !is_array($value) && !is_object($value)) {
            throw new FormValidationException("Expected scalar value for input, got " . gettype($value));
        }
    }
    
    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->serializeElementData();
    }
}
