
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\forms;

use pocketmine\form\Form as PMForm;
use pocketmine\player\Player;

abstract class Form implements PMForm {
    public const TYPE_MODAL = "modal";
    public const TYPE_MENU = "form";
    public const TYPE_CUSTOM_FORM = "custom_form";

    /** @var string */
    protected $title;

    /**
     * @param string $title
     */
    public function __construct(string $title) {
        $this->title = $title;
    }

    /**
     * Returns the form type used when sending this form to clients
     * @return string
     */
    abstract public function getType() : string;

    /**
     * Returns form data to send to the client
     * @return array
     */
    abstract protected function serializeFormData() : array;

    /**
     * @return array
     */
    final public function jsonSerialize() : array {
        return array_merge([
            "type" => $this->getType(),
            "title" => $this->title
        ], $this->serializeFormData());
    }

    /**
     * @return string
     */
    public function getTitle() : string {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title) : void {
        $this->title = $title;
    }

    /**
     * Called when a player submits the form
     * @param Player $player
     * @param mixed $data
     */
    abstract public function handleResponse(Player $player, $data) : void;
}
