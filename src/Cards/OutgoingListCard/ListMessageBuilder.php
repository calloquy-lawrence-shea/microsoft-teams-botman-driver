<?php

namespace MicrosoftTeamsDriver\Cards\OutgoingListCard;

use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use MicrosoftTeamsDriver\Cards\Actions\TapAction;
use MicrosoftTeamsDriver\Cards\OutgoingListCard\Items\ResultItem;

class ListMessageBuilder
{
    protected string $title = '';
    protected array $items = [];
    protected array $buttons = [];

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function hasItems(): bool
    {
        return count($this->items) > 0;
    }

    public function addItem(string $title, string $subtitle = '', ?string $imageUrl = null, ?TapAction $tapAction = null): self
    {
        $item = (new ResultItem(mb_strimwidth($title, 0, 30, "...")))
            ->setSubtitle(mb_strimwidth($subtitle, 0, 50, "..."))
            ->setIcon($imageUrl);

        if ($tapAction) {
            $item->setTap($tapAction);
        }

        $this->items[] = $item->getPayload();

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function addNextButton($value): self
    {
        $this->buttons[] = Button::create('Next')
            ->value($value)
            ->toArray();

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function addPrevButton($value): self
    {
        $this->buttons[] = Button::create('Previous')
            ->value($value)
            ->toArray();

        return $this;
    }

    public function clear(): void
    {
        $this->title = '';
        $this->items = [];
        $this->buttons = [];
    }

    public function getMessage(): OutgoingListMessage
    {
        $message = new OutgoingListMessage($this->title, $this->items);

        foreach ($this->buttons as $button) {
            $message->addButton($button);
        }

        return $message;
    }
}
