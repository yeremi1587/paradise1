
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
            return;
        }

        if (!is_array($data)) {
            $player->getServer()->getLogger()->error("Invalid form data type: " . gettype($data));
            $player->sendMessage("§cError processing the form. Please try again.");
            return;
        }

        try {
            // Extract non-label elements
            $nonLabelElements = [];
            foreach ($this->elements as $element) {
                if (!($element instanceof Label)) {
                    $nonLabelElements[] = $element;
                }
            }
            
            // Collect processable data for the response
            $processedData = [];
            
            // Process the data for each element
            foreach ($data as $index => $value) {
                if (!isset($nonLabelElements[$index])) {
                    $player->getServer()->getLogger()->warning("Form data index $index out of bounds");
                    continue;
                }

                $element = $nonLabelElements[$index];
                $processedData[$index] = $value;
                
                try {
                    // Validate and set value
                    $element->validateValue($value);
                    $element->setValue($value);
                } catch (FormValidationException $e) {
                    // Log the error but continue processing
                    $player->getServer()->getLogger()->error("Form validation error for element '{$element->getText()}': " . $e->getMessage());
                }
            }

            // Submit the form with both elements and processed data
            ($this->onSubmit)($player, new CustomFormResponse($this->elements, $processedData));
            
        } catch (\Throwable $e) {
            $player->getServer()->getLogger()->error("Error processing form: " . $e->getMessage());
            $player->getServer()->getLogger()->error($e->getTraceAsString());
            $player->sendMessage("§cAn error occurred while processing the form. Please try again.");
        }
    }
}
