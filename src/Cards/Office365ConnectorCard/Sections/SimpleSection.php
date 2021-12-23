<?php

namespace MicrosoftTeamsDriver\Cards\Office365ConnectorCard\Sections;

use Illuminate\Support\Collection;

class SimpleSection implements Section
{
    protected string $title;
    protected string $subtitle;
    protected string $text;
    protected string $image;

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getPayload(): Collection
    {
        return collect([
            'activityTitle' => $this->title,
            'activitySubtitle' => $this->subtitle,
            'activityText' => $this->text,
            'activityImage' => $this->image
        ]);
    }
}
