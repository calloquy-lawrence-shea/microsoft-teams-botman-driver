<?php

namespace MicrosoftTeamsDriver\Cards\OutgoingAdaptiveCard\Items;

use Illuminate\Support\Collection;

class AdaptiveInputTextCardItem extends AdaptiveCardItem
{
    public const TYPE_TEXT_INPUT = 'Input.Text';

    protected string $id;
    protected string $label;
    protected ?string $style;
    protected ?string $placeholder = null;

    public function __construct(string $label, string $id, ?string $style = null)
    {
        $this->label = $label;
        $this->id = $id;
        $this->style = $style;
    }

    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function getPayload(): Collection
    {
        return collect([
            'type' => self::TYPE_TEXT_INPUT,
            'id' => $this->id,
            'style' => $this->style,
            'label' => $this->label,
            'placeholder' => $this->placeholder
        ]);
    }
}

