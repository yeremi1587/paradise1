
<?php

namespace MihaiChirculete\WorldGuard\elements;

use pocketmine\form\FormValidationException;

class Toggle extends Element{

    /** @var bool */
    protected $default;

    /**
     * @param string $text
     * @param bool $default
     */
    public function __construct(string $text, bool $default = false, string $key = null){
        parent::__construct($text);
        $this->default = $default;
        if($key !== null){
            $this->setKey($key);
        }
    }

    /**
     * @return string
     */
    public function getType() : string{
        return "toggle";
    }

    /**
     * @return bool
     */
    public function getValue() : bool{
        // Handle null values by returning the default
        if ($this->value === null) {
            return $this->default;
        }
        
        // Handle various value types to ensure we always return a boolean
        if (is_string($this->value)) {
            return $this->value === "true" || $this->value === "1";
        }
        
        if (is_int($this->value)) {
            return $this->value !== 0;
        }
        
        return (bool)$this->value;
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
        // Toggle values should be booleans
        if(!is_bool($value) && !is_int($value) && !is_string($value)){
            throw new FormValidationException("Expected bool, int or string for toggle value, got " . gettype($value));
        }
    }

    /**
     * @return array
     */
    public function serializeElementData() : array{
        return [
            "type" => $this->getType(),
            "text" => $this->getText(),
            "default" => $this->default
        ];
    }
    
    /**
     * @return array
     */
    public function jsonSerialize() : array {
        return $this->serializeElementData();
    }
}

