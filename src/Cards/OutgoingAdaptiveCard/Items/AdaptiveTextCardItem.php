<?php

namespace MicrosoftTeamsDriver\Cards\OutgoingAdaptiveCard\Items;

use Illuminate\Support\Collection;

class AdaptiveTextCardItem extends AdaptiveCardItem
{
    public const TYPE_TEXT = 'TextBlock';

    protected string $text;
    protected ?string $size = null;
    protected ?string $weight = null;
    protected ?bool $wrap = null;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function setWrap(bool $value): self
    {
        $this->wrap = $value;
        return $this;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getPayload(): Collection
    {
        return collect([
            'type' => self::TYPE_TEXT,
            'text' => $this->text,
            'wrap' => $this->wrap,
            'size' => $this->size,
            'weight' => $this->weight
        ]);
    }
}
