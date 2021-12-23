<?php

namespace MicrosoftTeamsDriver\Cards\OutgoingAdaptiveCard;

use MicrosoftTeamsDriver\Cards\Actions\ActionTypeEnum;
use MicrosoftTeamsDriver\Cards\CardMessage;
use MicrosoftTeamsDriver\Cards\OutgoingAdaptiveCard\Items\AdaptiveInputTextCardItem;
use MicrosoftTeamsDriver\Cards\OutgoingAdaptiveCard\Items\AdaptiveTextCardItem;

class FormMessageBuilder
{
    protected array $itemsBody = [];
    protected array $actions = [];

    public function setTitle(string $title): self
    {
        array_unshift($this->itemsBody, (new AdaptiveTextCardItem($title))
            ->setSize('Large')
            ->setWeight('bolder')
            ->getPayload());

        return $this;
    }

    public function addText(string $text): self
    {
        $this->itemsBody[] = (new AdaptiveTextCardItem($text))
            ->setWrap(true)
            ->getPayload();

        return $this;
    }

    public function addTextInput(string $key, string $label, string $placeholder, ?string $style = null): self
    {
        $this->itemsBody[] = (new AdaptiveInputTextCardItem($label, $key, $style))
            ->setPlaceholder($placeholder)
            ->getPayload();

        return $this;
    }

    public function addOpenUrlButton(string $title, string $value): self
    {
        $this->actions[] = collect([
            'type' => ActionTypeEnum::ACTION_OPEN_URL,
            'title' => $title,
            'url' => $value
        ]);

        return $this;
    }

    public function addSubmitButton(string $title): self
    {
        $this->actions[] = collect([
            'type' => ActionTypeEnum::ACTION_SUBMIT,
            'title' => $title
        ]);

        return $this;
    }

    public function addSeparateInput(string $key, string $label, string $placeholder, string $text): self
    {
        $input = (new AdaptiveInputTextCardItem($label, $key))
            ->setPlaceholder($placeholder)
            ->getPayload();

        $emptyTextBlock = (new AdaptiveTextCardItem('⠀'))
            ->setWrap(false)
            ->getPayload();

        $textBlock = (new AdaptiveTextCardItem($text))
            ->setWrap(true)
            ->getPayload();

        $this->itemsBody[] = collect([
            'type' => OutgoingAdaptiveCardMessage::COLUMN_SET_TYPE,
            'columns' => [
                collect([
                    'type' => OutgoingAdaptiveCardMessage::COLUMN_TYPE,
                    'width' => 'auto',
                    'items' => [
                        $input
                    ]
                ]),
                collect([
                    'type' => OutgoingAdaptiveCardMessage::COLUMN_TYPE,
                    'width' => 'stretch',
                    'items' => [
                        $emptyTextBlock,
                        $textBlock
                    ]
                ])
            ]
        ]);

        return $this;
    }

    public function clear(): void
    {
        $this->actions = [];
        $this->itemsBody = [];
    }

    public function getMessage(): CardMessage
    {
        $cardBody = new AdaptiveCardBody(AdaptiveCardBody::TYPE_BODY_ITEMS, $this->itemsBody);
        return new OutgoingAdaptiveCardMessage($cardBody, $this->actions);
    }
}
