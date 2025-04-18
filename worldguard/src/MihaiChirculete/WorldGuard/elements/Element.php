
<?php

namespace MihaiChirculete\WorldGuard\elements;

abstract class Element{

    /** @var string */
    protected $text;
    /** @var string */
    protected $key;
    /** @var mixed */
    protected $value;

    /**
     * @param string $text
     */
    public function __construct(string $text, string $key = null){
        $this->text = $text;
        $this->key = $key ?? $text;
    }

    /**
     * Returns the element's label.
     * @return string
     */
    public function getText() : string{
        return $this->text;
    }

    /**
     * Returns the element's key. Used for accessing results in CustomForm::onSubmit().
     * @return string
     */
    public function getKey() : string{
        return $this->key;
    }

    /**
     * Sets the key of the element.
     * @param string $key
     *
     * @return Element
     */
    public function setKey(string $key) : Element{
        $this->key = $key;
        return $this;
    }

    /**
     * Returns the element's type.
     * @return string
     */
    abstract public function getType() : string;

    /**
     * Returns the element's value. Value depends on the element type.
     * @return mixed
     */
    abstract public function getValue();

    /**
     * Sets the element's value. Used when re-sending forms.
     * @param mixed $value
     */
    public function setValue($value) : void{
        $this->value = $value;
    }

    /**
     * Validates that the input is correct for the specific element type.
     * Throws FormValidationException if not.
     *
     * @param mixed $value
     * @return void
     */
    public function validateValue($value) : void{
        // Default implementation - accept any value
        // Subclasses should override this to provide proper validation
    }

    /**
     * Serializes the element to JSON for sending to clients.
     * @return array
     */
    public function jsonSerialize() : array{
        $jsonData = [
            "type" => $this->getType(),
            "text" => $this->getText()
        ];

        return array_merge($jsonData, $this->serializeElementData());
    }

    /**
     * Returns additional data needed for serializing the specific element type.
     * @return array
     */
    abstract public function serializeElementData() : array;
}
