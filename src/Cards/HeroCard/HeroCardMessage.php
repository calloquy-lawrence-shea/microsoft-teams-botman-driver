<?php

namespace MicrosoftTeamsDriver\Cards\HeroCard;

use App\Services\MicrosoftBot\CardActions\TapAction;
use App\Services\MicrosoftBot\MessageTemplates\CardMessage;

/**
 * @see https://docs.microsoft.com/en-us/microsoftteams/platform/task-modules-and-cards/cards/cards-reference#hero-card
 */
class HeroCardMessage implements CardMessage
{
    public const CONTENT_TYPE = 'application/vnd.microsoft.card.hero';

    protected string $title = '';
    protected string $subtitle = '';
    protected string $text = '';
    protected array $images = [];
    protected array $buttons = [];

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

    public function addButton(string $typeAction, string $title, string $value): self
    {
        $action = new TapAction($typeAction, $value, $title);
        $this->buttons[] = $action->getPayload();

        return $this;
    }

    public function addImage(?string $url): self
    {
        $this->images[] = collect([
            'url' => $url
        ]);

        return $this;
    }

    public function getContent(): array
    {
        return [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'text' => $this->text,
            'images' => $this->images,
            'buttons' => $this->buttons
        ];
    }
}
