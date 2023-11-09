<?php
namespace Speroteck\Task4\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteFactory;

class OrderPlaceBefore implements ObserverInterface
{
    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * OrderPlaceBefore constructor
     *
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(
        QuoteFactory $quoteFactory
    ) {
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getOrder();
        $quote = $this->quoteFactory->create()->loadByIdWithoutStore($order->getQuoteId());
        $order->setData('certificate_value', $quote->getData('certificate_value'));
        $order->setData('delivery_date', $quote->getData('delivery_date'));
    }
}
