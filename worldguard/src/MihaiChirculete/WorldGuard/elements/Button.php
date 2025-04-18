
<?php
declare(strict_types=1);

namespace MihaiChirculete\WorldGuard\elements;
class Button extends Element
{
    /** @var Image|null */
    protected $image;
    /** @var string */
    protected $type;

    /**
     * @param string $text
     * @param Image|null $image
     */
    public function __construct(string $text, ?Image $image = null)
    {
        parent::__construct($text);
        $this->image = $image;
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
    public function getType(): string
    {
        return "button"; // Return a default type instead of null
    }

    /**
     * @return bool
     */
    public function hasImage(): bool
    {
        return $this->image !== null;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        // Button returns its text as value
        return $this->text;
    }

    /**
     * @return array
     */
    public function serializeElementData(): array
    {
        $data = ["text" => $this->text];
        if ($this->hasImage()) {
            $data["image"] = $this->image;
        }
        return $data;
    }
}
