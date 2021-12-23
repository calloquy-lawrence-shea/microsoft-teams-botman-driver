<?php

namespace MicrosoftTeamsDriver\Cards\Office365ConnectorCard;

use MicrosoftTeamsDriver\Cards\Office365ConnectorCard\Sections\Section;

class ConnectorCardSections
{
    protected array $sections;

    public function addSection(Section $section): self
    {
        $this->sections[] = $section->getPayload();
        return $this;
    }

    public function getPayload(): array
    {
        return $this->sections;
    }
}
