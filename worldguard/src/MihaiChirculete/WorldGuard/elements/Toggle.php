
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\elements;

use pocketmine\form\FormValidationException;
use function is_bool;

class Toggle extends Element
{
    /** @var bool */
    protected $default;

    public function __construct(string $text, bool $default = false)
    {
        parent::__construct($text);
        $this->default = $default;
        $this->value = $default; // Initialize value with default to avoid null
    }

    /**
     * @return bool
     */
    public function getValue(): bool
    {
        $value = parent::getValue();
        
        // Handle null by returning default
        if ($value === null) {
            return $this->default;
        }

        // Convert string "true"/"false" to boolean
        if (is_string($value)) {
            if (strtolower($value) === "true" || $value === "1") {
                return true;
            } elseif (strtolower($value) === "false" || $value === "0") {
                return false;
            }
        }
        
        // Convert integer to boolean
        if (is_int($value)) {
            return $value !== 0;
        }
        
        // Ensure a boolean return
        return (bool)$value;
    }

    /**
     * @return bool
     */
    public function hasChanged(): bool
    {
        return $this->default !== $this->getValue();
    }

    /**
     * @return bool
     */
    public function getDefault(): bool
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return "toggle";
    }

    /**
     * @return array
     */
    public function serializeElementData(): array
    {
        return [
            "default" => $this->default
        ];
    }

    /**
     * @param mixed $value
     */
    public function validate($value): void
    {
        // Store value without validation, conversion will happen in getValue()
        $this->setValue($value);
    }
}
