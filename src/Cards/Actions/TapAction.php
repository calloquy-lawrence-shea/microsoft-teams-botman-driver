<?php

namespace MicrosoftTeamsDriver\Cards\Actions;

use Illuminate\Support\Collection;

class TapAction
{
    public const OPEN_URL = 'openUrl';
    public const IM_BACK = 'imBack';

    public const DEFAULT_ACTION_TITLE = 'Open';

    protected string $type;
    protected string $value;
    protected string $title;

    public function __construct(string $type, string $value, string $title = '')
    {
        $this->type = $type;
        $this->value = $value;
        $this->title = $title ?: static::DEFAULT_ACTION_TITLE;
    }

    public function getPayload(): Collection
    {
        return collect([
            'type' => $this->type,
            'title' => $this->title,
            'value' => $this->value
        ]);
    }
}
