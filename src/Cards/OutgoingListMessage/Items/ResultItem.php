<?php

namespace App\Services\MicrosoftBot\MessageTemplates\OutgoingListMessage\Items;

use App\Services\MicrosoftBot\CardActions\TapAction;
use Illuminate\Support\Collection;

class ResultItem
{
    public const TYPE = 'resultItem';

    protected string $type = self::TYPE;
    protected string $title;
    protected ?string $icon = null;
    protected ?string $subtitle = null;
    protected ?TapAction $tap = null;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    public function setIcon(?string $iconUrl): self
    {
        $this->icon = $iconUrl;
        return $this;
    }

    public function setTap(TapAction $tapAction): self
    {
        $this->tap = $tapAction;
        return $this;
    }

    public function getPayload(): Collection
    {
        return collect([
            'type' => self::TYPE,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'icon' => $this->icon,
            'tap' => $this->tap ? $this->tap->getPayload() : null
        ]);
    }
}
