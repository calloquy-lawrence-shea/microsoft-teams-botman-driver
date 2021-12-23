<?php

namespace Tests;

use BotMan\BotMan\Drivers\Events\GenericEvent;
use BotMan\BotMan\Http\Curl;
use BotMan\BotMan\Interfaces\HttpInterface;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use MicrosoftTeamsDriver\Cards\OutgoingListCard\OutgoingListMessage;
use MicrosoftTeamsDriver\MicrosoftTeamsDriver;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class MicrosoftTeamsDriverTest extends TestCase
{
    private const USER_ID = '29:1zPNq1EP2_H-mik_1MQgKYp0nZu9tUljr2VEdTlGhEo7VlZ1YVDVSUZ0g70sk1';

    public function testHasMatchingEvent(): void
    {
        $driver = $this->getDriver($this->getResponseData('messageReaction'));
        $event = $driver->hasMatchingEvent();

        $this->assertInstanceOf(GenericEvent::class, $event);
        $this->assertEquals('messageReaction', $event->getName());
    }

    public function testBuildServicePayload(): void
    {
        $driver = $this->getDriver($this->getResponseData('message'));

        $listMessage = new OutgoingListMessage('List message');

        $message = new IncomingMessage($listMessage, self::USER_ID, self::USER_ID);

        $payload = $driver->buildServicePayload($message, $message);

        $this->assertInstanceOf(OutgoingListMessage::class, $payload['text']->getText());
    }

    public function testGetConversationAnswer(): void
    {
        $driver = $this->getDriver($this->getResponseData('message'));

        $user = '29:1zPNq1EP2_H-mik_1MQgKYp0nZu9tUljr2VEdTlGhEo7VlZ1YVDVSUZ0g70sk1';

        $message = new IncomingMessage('Test', $user, $user, new ParameterBag([
            'value' => [
                'value' => 'Test'
            ]
        ]));

        $answer = $driver->getConversationAnswer($message);

        $this->assertEquals('Test', $answer->getValue());
    }

    private function getDriver(array $responseData, HttpInterface $htmlInterface = null): MicrosoftTeamsDriver
    {
        $request = Mockery::mock(Request::class.'[getContent]');
        $request->shouldReceive('getContent')->andReturn(json_encode($responseData, JSON_THROW_ON_ERROR));

        if ($htmlInterface === null) {
            $htmlInterface = Mockery::mock(Curl::class);
        }

        return new MicrosoftTeamsDriver($request, [], $htmlInterface);
    }


    private function getResponseData(string $type): array
    {
        return [
            'type' => $type,
            'id' => '4IIOjFkzcYy1HbYO',
            'timestamp' => '2016-11-29T21:58:31.879Z',
            'serviceUrl' => 'https://smba.trafficmanager.net/emea/',
            'channelId' => 'msteams',
            'from' => [
                'id' => '29:1zPNq1EP2_H-mik_1MQgKYp0nZu9tUljr2VEdTlGhEo7VlZ1YVDVSUZ0g70sk1',
                'name' => 'Test',
            ],
            'conversation' => [
                'id' => '29:1zPNq1EP2_H-mik_1MQgKYp0nZu9tUljr2VEdTlGhEo7VlZ1YVDVSUZ0g70sk1',
            ],
            'recipient' => [
                'id' => '28:a91af6d0-0e59-4adb-abcf-b6a1f728e3f3',
                'name' => 'BotMan',
            ],
            'text' => 'hey there',
            'entities' => [],
        ];
    }
}
