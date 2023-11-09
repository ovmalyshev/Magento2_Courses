<?php

namespace Speroteck\Task3\Model;

/**
 * Pay In Store payment method model
 */
class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'custompayment';

    /**
     * Can Authorize property
     *
     * @var string
     */
    protected $_canAuthorize = 'true';
}
