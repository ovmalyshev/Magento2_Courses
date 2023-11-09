<?php
namespace Speroteck\Task4\Model;

use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;
use Speroteck\Task4\Api\DeliveryDateInterface;

class DeliveryDate implements DeliveryDateInterface
{
    /**
     * Checkout Session property
     *
     * @var Session
     */
    private $checkoutSession;

    /**
     * Logger property
     *
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * DeliveryDate constructor
     *
     * @param Session $checkoutSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session $checkoutSession,
        LoggerInterface $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    /**
     * Set Delivery Date
     *
     * @param string $certificateValue
     * @param string $deliveryDate
     * @return bool
     */
    public function setDeliveryDate($certificateValue, $deliveryDate)
    {
        try {
            $this->checkoutSession->getQuote()->setData('certificate_value', $certificateValue)->save();
            $this->checkoutSession->getQuote()->setData('delivery_date', $deliveryDate)->save();
            return true;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return false;
    }
}
