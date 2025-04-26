
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\forms;

use MihaiChirculete\WorldGuard\elements\Element;
use MihaiChirculete\WorldGuard\elements\Label;

class CustomFormResponse {
    /** @var Element[] */
    private $elements;
    
    /** @var array */
    private $data;

    /**
     * @param Element[] $elements
     * @param array $data
     */
    public function __construct(array $elements, array $data) {
        $this->elements = $elements;
        $this->data = $data;
    }

    /**
     * Returns an array of element values excluding Labels
     * @return array
     */
    public function getValues(): array {
        $values = [];
        
        foreach ($this->elements as $element) {
            if (!($element instanceof Label)) {
                $values[] = $element->getValue();
            }
        }
        
        return $values;
    }

    /**
     * Returns the value of a specific element by its key
     * @param string $key
     * @return mixed|null
     */
    public function getValueByKey(string $key) {
        foreach ($this->elements as $element) {
            if ($element->getKey() === $key) {
                return $element->getValue();
            }
        }
        
        return null;
    }

    /**
     * Returns an associative array of element keys and values
     * @return array
     */
    public function getAssociativeValues(): array {
        $values = [];
        
        foreach ($this->elements as $element) {
            $key = $element->getKey();
            if ($key !== null && !($element instanceof Label)) {
                $values[$key] = $element->getValue();
            }
        }
        
        return $values;
    }

    /**
     * @return Element[]
     */
    public function getElements(): array {
        return $this->elements;
    }

    /**
     * @return array
     */
    public function getRawData(): array {
        return $this->data;
    }
}
