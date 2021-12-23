<?php

namespace MicrosoftTeamsDriver\Cards\OutgoingAdaptiveCard;

use MicrosoftTeamsDriver\Cards\CardMessage;

/**
 * @see https://docs.microsoft.com/en-us/microsoftteams/platform/task-modules-and-cards/cards/cards-reference#adaptive-card
 */
class OutgoingAdaptiveCardMessage implements CardMessage
{
    public const CONTENT_TYPE = 'application/vnd.microsoft.card.adaptive';
    public const SCHEMA = 'https://adaptivecards.io/schemas/adaptive-card.json';
    public const TYPE = 'AdaptiveCard';

    public const COLUMN_SET_TYPE = 'ColumnSet';
    public const COLUMN_TYPE = 'Column';

    protected AdaptiveCardBody $cardBody;
    protected array $actions;

    public function __construct(AdaptiveCardBody $cardBody, array $actions = [])
    {
        $this->cardBody = $cardBody;
        $this->actions = $actions;
    }

    public function getContent(): array
    {
        return [
            '$schema' => static::SCHEMA,
            'version' => '1.2',
            'type'    => static::TYPE,
            'body'    => [$this->cardBody->getPayload()],
            'actions' => $this->actions
        ];
    }
}
