
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\forms;

use Closure;
use MihaiChirculete\WorldGuard\elements\Element;
use MihaiChirculete\WorldGuard\elements\Label;
use pocketmine\{form\FormValidationException, player\Player, utils\Utils};
use function array_merge;
use function gettype;
use function is_array;

class CustomForm extends Form
{
    /** @var Element[] */
    protected $elements;
    /** @var Closure */
    private $onSubmit;
    /** @var Closure|null */
    private $onClose;

    /**
     * @param string $title
     * @param Element[] $elements
     * @param Closure $onSubmit
     * @param Closure|null $onClose
     */
    public function __construct(string $title, array $elements, Closure $onSubmit, ?Closure $onClose = null)
    {
        parent::__construct($title);
        $this->elements = $elements;
        Utils::validateCallableSignature(function (Player $player, CustomFormResponse $response): void {
        }, $onSubmit);
        $this->onSubmit = $onSubmit;
        if ($onClose !== null) {
            Utils::validateCallableSignature(function (Player $player): void {
            }, $onClose);
            $this->onClose = $onClose;
        }
    }

    /**
     * @param Element ...$elements
     *
     * @return $this
     */
    public function append(Element ...$elements): self
    {
        $this->elements = array_merge($this->elements, $elements);
        return $this;
    }

    /**
     * @return string
     */
    final public function getType(): string
    {
        return self::TYPE_CUSTOM_FORM;
    }

    /**
     * @return array
     */
    protected function serializeFormData(): array
    {
        return ["content" => $this->elements];
    }

    final public function handleResponse(Player $player, $data): void
    {
        if ($data === null) {
            if ($this->onClose !== null) {
                ($this->onClose)($player);
            }
        } elseif (is_array($data)) {
            try {
                foreach ($data as $index => $value) {
                    if (!isset($this->elements[$index])) {
                        throw new FormValidationException("Element at index $index does not exist");
                    }
                    
                    $element = $this->elements[$index];
                    
                    // Skip validation for Label elements as they are display-only
                    if (!($element instanceof Label)) {
                        try {
                            $element->validate($value);
                        } catch (FormValidationException $e) {
                            // Log the validation error
                            $player->getServer()->getLogger()->error("Form validation error for element '{$element->getText()}': " . $e->getMessage());
                            
                            // Try to make a best-effort conversion based on the expected type
                            if ($element->getType() === "toggle" && is_string($value)) {
                                // Convert string to boolean for toggle elements
                                $value = ($value === "true" || $value === "1");
                                $element->setValue($value);
                            } elseif ($element->getType() === "input" && is_bool($value)) {
                                // Convert boolean to string for input elements
                                $value = $value ? "true" : "false";
                                $element->setValue($value);
                            } else {
                                // Re-throw the exception if we can't handle this conversion
                                throw $e;
                            }
                        }
                    }
                    
                    $element->setValue($value);
                }
                
                ($this->onSubmit)($player, new CustomFormResponse($this->elements));
            } catch (FormValidationException $e) {
                // Log the error without crashing
                $player->getServer()->getLogger()->error("Form validation error: " . $e->getMessage());
                $player->sendMessage("§cError al procesar el formulario. Por favor, inténtalo de nuevo.");
            }
        } else {
            throw new FormValidationException("Expected array or null, got " . gettype($data));
        }
    }
}
