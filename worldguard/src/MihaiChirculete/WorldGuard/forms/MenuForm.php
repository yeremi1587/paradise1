
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\forms;

use Closure;
use MihaiChirculete\WorldGuard\elements\Button;
use pocketmine\player\Player;
use pocketmine\utils\Utils;
use function array_merge;
use function count;
use function is_int;

class MenuForm extends Form {
    /** @var string */
    private $content;
    /** @var Button[] */
    private $buttons = [];
    /** @var Closure */
    private $onSubmit;
    /** @var Closure|null */
    private $onClose;

    /**
     * @param string $title
     * @param string $content
     * @param Button[] $buttons
     * @param Closure $onSubmit
     * @param Closure|null $onClose
     */
    public function __construct(string $title, string $content, array $buttons, Closure $onSubmit, ?Closure $onClose = null){
        parent::__construct($title);
        $this->content = $content;
        $this->buttons = array_values($buttons); //prevent issues with out-of-order indexing
        
        // Assign button values if not already set
        foreach($this->buttons as $index => $button){
            if($button->getValue() === null){
                $button->setValue($index);
            }
        }
        
        Utils::validateCallableSignature(function(Player $player, Button $selected) : void{
        }, $onSubmit);
        $this->onSubmit = $onSubmit;
        if($onClose !== null){
            Utils::validateCallableSignature(function(Player $player) : void{
            }, $onClose);
            $this->onClose = $onClose;
        }
    }

    /**
     * @param Button[] $buttons
     *
     * @return self
     */
    public function append(Button ...$buttons) : self{
        $this->buttons = array_merge($this->buttons, $buttons);
        return $this;
    }

    /**
     * @return Button[]
     */
    public function getButtons() : array{
        return $this->buttons;
    }

    /**
     * @return string
     */
    public function getContent() : string{
        return $this->content;
    }

    /**
     * @param int $index
     *
     * @return Button|null
     */
    public function getButton(int $index) : ?Button{
        return $this->buttons[$index] ?? null;
    }

    /**
     * @param string $text
     * @param Closure $callback
     *
     * @return self
     */
    public function setButton(string $text, Closure $callback) : self{
        $button = count($this->buttons);
        $this->buttons[] = new Button($text);
        $this->buttons[$button]->setValue($button);
        $this->labeledButtonCallbacks[$button] = $callback;

        return $this;
    }

    /**
     * @return string
     */
    final public function getType() : string{
        return self::TYPE_MENU;
    }

    /**
     * @return array
     */
    protected function serializeFormData() : array{
        $buttonsData = [];
        foreach ($this->buttons as $button) {
            $buttonsData[] = $button;
        }
        
        return [
            "buttons" => $buttonsData,
            "content" => $this->content
        ];
    }

    final public function handleResponse(Player $player, $data) : void{
        if($data === null){
            if($this->onClose !== null){
                ($this->onClose)($player);
            }
        }elseif(is_int($data)){
            if(!isset($this->buttons[$data])){
                $player->getServer()->getLogger()->warning("Menu form " . $this->getTitle() . " returned invalid button index $data");
                return;
            }
            
            // Set the value to match the index if not already set
            $selectedButton = $this->buttons[$data];
            if($selectedButton->getValue() === null){
                $selectedButton->setValue($data);
            }
            
            ($this->onSubmit)($player, $selectedButton);
        }else{
            $player->getServer()->getLogger()->warning("Expected int or null, got " . gettype($data));
        }
    }
}
