
<?php

namespace MihaiChirculete\WorldGuard\elements;

class Toggle extends Element{

    /** @var bool */
    protected $default;

    /**
     * @param string $text
     * @param bool $default
     */
    public function __construct(string $text, bool $default = false, string $key = null){
        parent::__construct($text, $key);
        $this->default = $default;
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
     * @return array
     */
    public function serializeElementData() : array{
        return [
            "default" => $this->default
        ];
    }
}
