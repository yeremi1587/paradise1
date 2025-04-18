
<?php

namespace MihaiChirculete\WorldGuard\forms;

use MihaiChirculete\WorldGuard\elements\Element;
use pocketmine\Server;

class CustomFormResponse {

    /** @var Element[] */
    protected $elements;
    
    /** @var array */
    protected $data;

    /**
     * @param Element[] $elements
     * @param array $data
     */
    public function __construct(array $elements, array $data){
        $this->elements = $elements;
        $this->data = $data;

        // Validate the response data
        $this->validateResponse();
    }

    /**
     * Validates response data to ensure it matches expected element types.
     * Logs errors but allows execution to continue with defaults.
     */
    protected function validateResponse() : void {
        foreach($this->elements as $i => $element){
            if(!isset($this->data[$i])){
                Server::getInstance()->getLogger()->error("Form validation error: Missing data for element " . $element->getText());
                continue;
            }

            try {
                $element->validateValue($this->data[$i]);
                $element->setValue($this->data[$i]);
            } catch(\Exception $e) {
                // Log the error but don't crash
                Server::getInstance()->getLogger()->error("Form validation error for element '" . $element->getText() . "': " . $e->getMessage());
                
                // Set a default value appropriate for the element type
                switch($element->getType()) {
                    case "toggle":
                        $element->setValue(false);
                        break;
                    case "slider":
                    case "stepslider":
                        $element->setValue(0);
                        break;
                    case "dropdown":
                        $element->setValue(0);
                        break;
                    case "input":
                        $element->setValue("");
                        break;
                    default:
                        $element->setValue(null);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getValues() : array{
        $values = [];

        foreach($this->elements as $element){
            if ($element->getKey() !== null) {
                try {
                    $values[$element->getKey()] = $element->getValue();
                } catch (\Throwable $e) {
                    Server::getInstance()->getLogger()->error("Error getting value for element '" . $element->getText() . "': " . $e->getMessage());
                    
                    // Provide sensible defaults based on element type
                    switch($element->getType()) {
                        case "toggle":
                            $values[$element->getKey()] = false;
                            break;
                        case "slider":
                        case "stepslider":
                            $values[$element->getKey()] = 0;
                            break;
                        case "dropdown":
                            $values[$element->getKey()] = 0;
                            break;
                        case "input":
                            $values[$element->getKey()] = "";
                            break;
                        default:
                            $values[$element->getKey()] = null;
                    }
                }
            }
        }

        return $values;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getValue(string $key){
        $values = $this->getValues();
        return $values[$key] ?? null;
    }
}
