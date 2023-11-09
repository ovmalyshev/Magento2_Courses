<?php
namespace Speroteck\Task4\Api;

interface DeliveryDateInterface
{
    /**
     * Set Delivery Date
     *
     * @param string $certificateValue
     * @param string $deliveryDate
     * @return bool
     */
    public function setDeliveryDate($certificateValue, $deliveryDate);
}
