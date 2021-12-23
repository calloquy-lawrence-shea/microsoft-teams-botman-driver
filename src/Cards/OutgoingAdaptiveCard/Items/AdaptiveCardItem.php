<?php

namespace MicrosoftTeamsDriver\Cards\OutgoingAdaptiveCard\Items;

use Illuminate\Support\Collection;

abstract class AdaptiveCardItem
{
    abstract public function getPayload(): Collection;
}
