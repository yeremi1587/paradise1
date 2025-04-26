
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\elements;

use pocketmine\form\FormValidationException;

class Label extends Element {

    /** @var mixed */
    protected $value = null;

    /**
     * @param string $text
     */
    public function __construct(string $text) {
        parent::__construct($text);
        $this->type = "label";
    }

    /**
     * Returns the value of the element
     * @return mixed
     */
    public function getValue() {
        return $this->value;
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
        // Labels don't have values coming back from the form, so there's nothing to validate
    }

    /**
     * @return array
     */
    public function serializeElementData() : array {
        return [
            "type" => $this->type,
            "text" => $this->text
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array {
        return $this->serializeElementData();
    }
}
