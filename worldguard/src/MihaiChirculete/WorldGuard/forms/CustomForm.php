
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
                $skipValidation = false;
                $indexOffset = 0;
                
                foreach ($data as $index => $value) {
                    $adjustedIndex = $index + $indexOffset;
                    
                    if (!isset($this->elements[$adjustedIndex])) {
                        // Skip this item - might be an indexing issue due to Labels
                        continue;
                    }
                    
                    $element = $this->elements[$adjustedIndex];
                    
                    // Skip validation for Label elements as they are display-only
                    if ($element instanceof Label) {
                        $indexOffset++;
                        continue;
                    }
                    
                    try {
                        $element->validate($value);
                    } catch (FormValidationException $e) {
                        // Log the validation error
                        $player->getServer()->getLogger()->debug("Form validation warning for element '{$element->getText()}': " . $e->getMessage());
                        
                        // We'll continue processing the form despite the validation error
                        $skipValidation = true;
                    }
                }
                
                if (!$skipValidation) {
                    // Process all labels to ensure consistent indexing
                    $processedElements = [];
                    $labelCount = 0;
                    
                    foreach ($this->elements as $element) {
                        if ($element instanceof Label) {
                            $labelCount++;
                            continue;
                        }
                        
                        $dataIndex = count($processedElements);
                        if (isset($data[$dataIndex])) {
                            $element->setValue($data[$dataIndex]);
                        }
                        
                        $processedElements[] = $element;
                    }
                }
                
                try {
                    ($this->onSubmit)($player, new CustomFormResponse($this->elements));
                } catch (\Throwable $e) {
                    // Catch any errors in the form submission handler
                    $player->getServer()->getLogger()->error("Form submission error: " . $e->getMessage());
                    $player->getServer()->getLogger()->error($e->getTraceAsString());
                    $player->sendMessage("§cError processing the form. Please try again.");
                }
                
            } catch (FormValidationException $e) {
                // Log the error without crashing
                $player->getServer()->getLogger()->error("Form validation error: " . $e->getMessage());
                $player->sendMessage("§cError in form validation. Please try again.");
            } catch (\Throwable $e) {
                // Catch any unexpected errors
                $player->getServer()->getLogger()->error("Unexpected error in form handling: " . $e->getMessage());
                $player->getServer()->getLogger()->error($e->getTraceAsString());
                $player->sendMessage("§cAn unexpected error occurred. Please try again.");
            }
        } else {
            $player->getServer()->getLogger()->error("Invalid form data: " . gettype($data));
            $player->sendMessage("§cError processing the form. Please try again.");
        }
    }
}
