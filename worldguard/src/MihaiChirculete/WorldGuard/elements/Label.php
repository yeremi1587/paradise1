
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\elements;

use pocketmine\form\FormValidationException;

class Label extends Element
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return "label";
    }

    /**
     * @return array
     */
    public function serializeElementData(): array
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return null; // Labels don't have values
    }

    /**
     * Labels should accept any value without validation since they are just display elements
     * and don't actually store user input. This fixes issues with some Minecraft Bedrock clients
     * that send string values for labels instead of null.
     * 
     * @param mixed $value
     * @return void
     */
    public function validateValue($value): void
    {
        // Accept any value for labels - they are display-only elements
        // This fixes the "Expected null, got string" error
    }
}
