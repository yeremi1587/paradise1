
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\elements;

use pocketmine\form\FormValidationException;

abstract class Element implements \JsonSerializable {

    /** @var string */
    protected $text;
    /** @var mixed|null */
    protected $value = null;
    /** @var string|null */
    protected $key;
    /** @var string */
    protected $type;

    /**
     * @param string $text
     */
    public function __construct(string $text) {
        $this->text = $text;
    }

    /**
     * Returns the value of the element after the form is submitted.
     * @return mixed
     */
    abstract public function getValue();

    /**
     * Sets the value of the element. Used when interpreting response data.
     * @param mixed $value
     */
    abstract public function setValue($value): void;

    /**
     * Validates the value returned from the form
     * @param mixed $value
     * @throws FormValidationException if the value is invalid
     */
    abstract public function validateValue($value): void;

    /**
     * Serializes the element data to be sent to the client
     * @return array
     */
    abstract public function serializeElementData(): array;

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @param string|null $key
     * 
     * @return self
     */
    public function setKey(?string $key): self {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getKey(): ?string {
        return $this->key;
    }

    /**
     * Implementation of JsonSerializable
     * @return array
     */
    public function jsonSerialize(): array {
        return $this->serializeElementData();
    }
}
