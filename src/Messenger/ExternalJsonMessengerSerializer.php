<?php

namespace App\Messenger;

use App\Message\Command\LogEmoji;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class ExternalJsonMessengerSerializer implements SerializerInterface
{
    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];
        $headers = $encodedEnvelope['headers'];

        $data = json_decode($body, true);
        $message = new LogEmoji($data['emoji']);

        $envelope = new Envelope($message);
        //needed only if you need  this to be sent through the non default bus.
        $envelope = $envelope->with(new BusNameStamp('command.bus'));
        return $envelope;
    }

    public function encode(Envelope $envelope): array
    {
        throw new \Exception('Transport $ Serializer are not meant to sending messages');
    }

}