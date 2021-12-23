<?php

namespace MicrosoftTeamsDriver\Cards\OutgoingAdaptiveCard;

use Illuminate\Support\Collection;

class AdaptiveCardBody
{
    public const TYPE_BODY_ITEMS = 'Container';

    protected string $type;
    protected array $items;

    public function __construct(string $type, array $items = [])
    {
        $this->type = $type;
        $this->items = $items;
    }

    public function getPayload(): Collection
    {
        return collect([
            'type' => $this->type,
            'items' => $this->items
        ]);
    }
}
