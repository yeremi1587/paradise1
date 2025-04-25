
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\elements;

use pocketmine\form\FormValidationException;

class Button implements \JsonSerializable
{
    /** @var string */
    protected $text;
    /** @var Image|null */
    protected $image;
    /** @var mixed */
    protected $value;
    /** @var string */
    protected $type;

    /**
     * @param string $text
     * @param Image|null $image
     */
    public function __construct(string $text, ?Image $image = null)
    {
        $this->text = $text;
        $this->image = $image;
        $this->type = "button";
    }

    /**
     * @param string ...$texts
     *
     * @return Button[]
     */
    public static function createFromList(string ...$texts): array
    {
        $buttons = [];
        foreach ($texts as $text) {
            $buttons[] = new self($text);
        }
        return $buttons;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return bool
     */
    public function hasImage(): bool
    {
        return $this->image !== null;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function serializeElementData(): array
    {
        $data = ["text" => $this->getText()];
        if ($this->hasImage()) {
            $data["image"] = $this->image;
        }
        return $data;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = ["text" => $this->getText()];
        if ($this->hasImage()) {
            $data["image"] = $this->image;
        }
        return $data;
    }
}
