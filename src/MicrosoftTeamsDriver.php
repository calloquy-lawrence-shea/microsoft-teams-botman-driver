<?php

namespace MicrosoftTeamsDriver;

use BotMan\BotMan\Drivers\Events\GenericEvent;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Video;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\BotFramework\BotFrameworkDriver;
use Illuminate\Support\Collection;
use MicrosoftTeamsDriver\Cards\Actions\TapAction;
use MicrosoftTeamsDriver\Cards\HeroCard\HeroCardMessage;
use MicrosoftTeamsDriver\Cards\Office365ConnectorCard\Office365ConnectorMessage;
use MicrosoftTeamsDriver\Cards\OutgoingAdaptiveCard\OutgoingAdaptiveCardMessage;
use MicrosoftTeamsDriver\Cards\OutgoingListCard\OutgoingListMessage;
use Symfony\Component\HttpFoundation\ParameterBag;

class MicrosoftTeamsDriver extends BotFrameworkDriver
{
    public const CONVERSATION_UPDATE_EVENT = 'conversationUpdate';
    public const INSTALLATION_UPDATE_EVENT = 'installationUpdate';
    public const MESSAGE_REACTION_EVENT = 'messageReaction';

    public const GENERIC_EVENTS = [
        self::CONVERSATION_UPDATE_EVENT,
        self::INSTALLATION_UPDATE_EVENT,
        self::MESSAGE_REACTION_EVENT
    ];

    /**
     * @return GenericEvent|bool
     */
    public function hasMatchingEvent()
    {
        $event = false;

        foreach (self::GENERIC_EVENTS as $genericEvent) {
            if ($this->event->has('type') && $this->event->get('type') === $genericEvent) {
                $event = new GenericEvent($this->event->get($genericEvent));
                $event->setName($genericEvent);

                return $event;
            }
        }

        return $event;
    }

    public function buildServicePayload($message, $matchingMessage, $additionalParameters = [])
    {
        $parameters = array_merge_recursive([
            'type' => 'message',
        ], $additionalParameters);

        switch (true) {
            case $message instanceof Question:
                $this->setQuestionParameters($message, $parameters);
                break;
            case $message instanceof OutgoingMessage:
                $this->setOutgoingMessageParameters($message, $parameters);
                break;
            case $message instanceof OutgoingListMessage:
                $this->setOutgoingListMessage($message, $parameters);
                break;
            case $message instanceof OutgoingAdaptiveCardMessage:
                $this->setOutgoingAdaptiveCardMessage($message, $parameters);
                break;
            case $message instanceof Office365ConnectorMessage:
                $this->setOffice365ConnectorMessage($message, $parameters);
                break;
            case $message instanceof HeroCardMessage:
                $this->setHeroCardMessage($message, $parameters);
                break;
            default:
                $parameters['text'] = $message;
        }

        /**
         * Originated messages use the getSender method, otherwise getRecipient.
         */
        $recipient = $matchingMessage->getRecipient() === '' ? $matchingMessage->getSender() : $matchingMessage->getRecipient();
        $payload = is_null($matchingMessage->getPayload()) ? [] : $matchingMessage->getPayload()->all();
        $this->apiURL = Collection::make($payload)->get('serviceUrl',
                Collection::make($additionalParameters)->get('serviceUrl')).'/v3/conversations/'.urlencode($recipient).'/activities';

        if (strpos($this->apiURL, 'webchat.botframework') !== false) {
            $parameters['from'] = [
                'id' => $payload['recipient']['id'],
            ];
        }

        return $parameters;
    }

    public function setHeroCardMessage(HeroCardMessage $message, array &$parameters): void
    {
        $parameters['attachments'] = [
            [
                'contentType' => HeroCardMessage::CONTENT_TYPE,
                'content' => $message->getContent(),
            ],
        ];
    }

    public function setOffice365ConnectorMessage(Office365ConnectorMessage $message, array &$parameters): void
    {
        $parameters['attachments'] = [
            [
                'contentType' => Office365ConnectorMessage::CONTENT_TYPE,
                'content' => $message->getContent(),
            ],
        ];
    }

    /**
     * @param mixed $message
     * @param array $parameters
     */
    private function setOutgoingAdaptiveCardMessage($message, array &$parameters): void
    {
        $parameters['attachments'] = [
            [
                'contentType' => OutgoingAdaptiveCardMessage::CONTENT_TYPE,
                'content' => $message->getContent(),
            ],
        ];
    }

    /**
     * @param mixed $message
     * @param array $parameters
     */
    private function setQuestionParameters($message, array &$parameters): void
    {
        $parameters['attachments'] = [
            [
                'contentType' => HeroCardMessage::CONTENT_TYPE,
                'content' => [
                    'text' => $message->getText(),
                    'buttons' => $this->convertQuestion($message),
                ],
            ],
        ];
    }

    /**
     * @param mixed $message
     * @param array $parameters
     */
    private function setOutgoingMessageParameters($message, array &$parameters): void
    {
        $parameters['text'] = $message->getText();
        $attachment = $message->getAttachment();

        if (!is_null($attachment)) {
            if ($attachment instanceof Image) {
                $parameters['attachments'] = [
                    [
                        'contentType' => 'image/png',
                        'contentUrl' => $attachment->getUrl(),
                    ],
                ];
            } elseif ($attachment instanceof Video) {
                $parameters['attachments'] = [
                    [
                        'contentType' => 'video/mp4',
                        'contentUrl' => $attachment->getUrl(),
                    ],
                ];
            } elseif ($attachment instanceof File) {
                $parameters['attachments'] = $this->getAttachmentFileParameters($attachment);
            }
        }
    }

    private function getAttachmentFileParameters(File $file): array
    {
        $payload = $file->getPayload();

        $action = new TapAction(TapAction::OPEN_URL, $file->getUrl(), $payload['title'] ?: '');

        return [
            [
                'contentType' => 'application/vnd.microsoft.card.hero',
                'content' => [
                    'buttons' => [
                        $action->getPayload()
                    ]
                ]
            ],
        ];
    }

    /**
     * @param mixed $message
     * @param array $parameters
     */
    private function setOutgoingListMessage($message, array &$parameters): void
    {
        $content = $message->getContent();

        $content['buttons'] = $this->convertQuestion($message);

        $parameters['attachments'] = [
            [
                'contentType' => 'application/vnd.microsoft.teams.card.list',
                'content' => $content,
            ],
        ];
    }

    protected function convertQuestion($message): array
    {
        $replies = Collection::make($message->getButtons())->map(function ($button) {
            return array_merge([
                'type' => 'messageBack',
                'title' => $button['text'],
                'displayText' => $button['text'],
                'value' => "{\"value\": \"{$button['value']}\"}",
            ], $button['additional']);
        });

        return $replies->toArray();
    }

    public function getConversationAnswer(IncomingMessage $message): Answer
    {
        /** @var null|ParameterBag $payload */
        $payload = $message->getPayload();

        if ($payload) {
            $value = $payload->get('value');

            if (is_array($value)) {
                return Answer::create($message->getText())
                    ->setInteractiveReply(true)
                    ->setMessage($message)
                    ->setValue($value['value'] ?? $value);
            }
        }

        return Answer::create($message->getText())->setMessage($message);
    }
}