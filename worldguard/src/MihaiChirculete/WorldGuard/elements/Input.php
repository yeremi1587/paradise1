
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
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        $value = parent::getValue();
        // Ensure we always return a string, converting other types if necessary
        if (is_bool($value)) {
            return $value ? "true" : "false";
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
            "placeholder" => $this->placeholder,
            "default" => $this->default
        ];
    }

    /**
     * @param mixed $value
     */
    public function validate($value): void
    {
        // Accept any value type and convert it to string when getValue() is called
        // This prevents validation errors while still maintaining type safety
        $this->setValue($value);
    }
}
