<?php
namespace Speroteck\Task11CronJob\Cron;

use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use Speroteck\Task11CronJob\Model\Queue\Publisher\Publisher;

class CheckoutEmail
{

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $quoteCollectionFactory;

    /**
     * @var Publisher
     */
    private Publisher $publisher;
    private \Magento\Quote\Api\CartRepositoryInterface $cartRepository;

    /**
     * CheckoutEmail constructor.
     * @param CollectionFactory $quoteCollectionFactory
     */
    public function __construct(
        CollectionFactory $quoteCollectionFactory,
        Publisher $publisher,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->publisher = $publisher;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Cronjob Description
     *
     * @return void
     */
    public function execute(): void
    {
        $quotes = $this->getQuote()->getData();
        foreach ($quotes as $quote => $data) {
            $this->publisher->execute($data['entity_id']);
        }
    }

    public function getQuote()
    {
        /**
         * @var $quoteCollection
         */
        $quoteCollection = $this->quoteCollectionFactory->create();
        $quoteCollection->addFieldToFilter('is_active', 1);
        $quoteCollection->addFieldToFilter('customer_email', ['notnull' => true]);

        return $quoteCollection;
    }
}
