<?php

namespace App\MessageHandler\Command;

use App\Message\Command\LogEmoji;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class LogEmojiHandler implements MessageHandlerInterface
{
    private static $emojis = [
        '🔥',
        '🚀',
        '✅',
        '💡',
        '🎯'
    ];
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(LogEmoji  $logEmoji)
    {
        $index = $logEmoji->getEmojiIndex();
        //if index exists use it otherwise use $emoji[0]
        $emoji = self::$emojis[$index] ?? self::$emojis[0];
        $this->logger->info('Important Message !!'.$emoji);
    }
}