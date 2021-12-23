<?php

namespace App\Services\MicrosoftBot\MessageTemplates\Office365ConnectorCard;


use MicrosoftTeamsDriver\Cards\CardMessage;
use MicrosoftTeamsDriver\Cards\Office365ConnectorCard\ConnectorCardActions;
use MicrosoftTeamsDriver\Cards\Office365ConnectorCard\ConnectorCardSections;
use MicrosoftTeamsDriver\Cards\Office365ConnectorCard\Sections\SimpleSection;

/**
 * @see https://docs.microsoft.com/en-us/microsoftteams/platform/task-modules-and-cards/cards/cards-reference#office-365-connector-card
 */
class Office365ConnectorMessage implements CardMessage
{
    public const CONTENT_TYPE = 'application/vnd.microsoft.teams.card.o365connector';

    protected string $summary = '';
    protected string $title = '';

    protected ConnectorCardSections $cardSections;
    protected ConnectorCardActions $potentialActions;

    public function __construct(ConnectorCardActions $cardActions, ConnectorCardSections $cardSections)
    {
        $this->potentialActions = $cardActions;
        $this->cardSections = $cardSections;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setSections(ConnectorCardSections $cardSections): self
    {
        $this->cardSections = $cardSections;
        return $this;
    }

    public function setActions(ConnectorCardActions $cardActions): self
    {
        $this->potentialActions = $cardActions;
        return $this;
    }

    public function addSimpleSection(string $title, string $subtitle, string $text, string $image = ''): self
    {
        $section = (new SimpleSection())
            ->setTitle($title)
            ->setSubtitle($subtitle)
            ->setText($text);

        if ($image) {
            $section->setImage($image);
        }

        $this->cardSections->addSection($section);

        return $this;
    }

    public function addViewAction(string $title, string $url): self
    {
        $this->potentialActions->addAction(ConnectorCardActions::VIEW_ACTION, $title, [$url]);
        return $this;
    }

    public function addImBackAction(string $title, $value): self
    {
        $this->potentialActions->addInvokeAction($title, $value);
        return $this;
    }

    public function getContent(): array
    {
        return [
            '@type' => 'MessageCard',
            '@context' => 'http://schema.org/extensions',
            'title' => $this->title,
            'summary' => $this->summary,
            'sections' => $this->cardSections->getPayload(),
            'potentialAction' => $this->potentialActions->getPayload()
        ];
    }
}
