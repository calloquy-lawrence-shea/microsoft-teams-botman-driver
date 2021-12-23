<?php

namespace MicrosoftTeamsDriver\Cards\Office365ConnectorCard\Sections;

use Illuminate\Support\Collection;

interface Section
{
    public function getPayload(): Collection;
}
