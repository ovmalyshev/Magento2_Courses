<?php

namespace Speroteck\Task11CronJob\Model\Queue\Publisher;

use Magento\Framework\MessageQueue\PublisherInterface;

class Publisher
{
    const TOPIC_NAME = 'email.finish.checkout';

    /**
     * @var PublisherInterface
     */
    private $publisher;

    public function __construct(
        PublisherInterface $publisher
    ) {
        $this->publisher=$publisher;
    }

    public function execute($email)
    {
        $this->publisher->publish(self::TOPIC_NAME, $email);
    }
}
