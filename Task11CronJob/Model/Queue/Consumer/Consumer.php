<?php

namespace Speroteck\Task11CronJob\Model\Queue\Consumer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Consumer extends AbstractHelper
{
    protected StateInterface $inlineTranslation;
    protected Escaper $escaper;
    protected TransportBuilder $transportBuilder;
    protected LoggerInterface $logger;
    protected $scopeConfig;
    protected StoreManagerInterface $storeManager;
    private CartRepositoryInterface $cartRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        CartRepositoryInterface $cartRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->cartRepository = $cartRepository;
        $this->logger = $logger;
    }

    public function process($quoteId)
    {
        $customerEmail =  'omalyshev@speroteck.com';

        $itemsParameters = [];
        $items = $this->cartRepository->get($quoteId)->getItems();
        $i=0;
        foreach ($items as $item) {
            $data = $item->getData();
            $itemsParameters['items'][$i]['item_id'] = $data['item_id'];
            $itemsParameters['items'][$i]['name'] = $data['name'];
            $itemsParameters['items'][$i]['qty'] = $data['qty'];
            $i++;
        }
        $totalPrice = $this->cartRepository->get($quoteId)->getSubtotal();
        $currency = $this->cartRepository->get($quoteId)->getCurrency()->getData()['quote_currency_code'];
        $itemsParameters['totalPrice'] = (int)$totalPrice;
        $itemsParameters['currency'] = $currency;

        try {
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => 'Magento',
                'email' => 'omalyshev@speroteck.com'
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('email_template')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
                    ]
                )
                ->setTemplateVars(
                    [
                    'itemsParameters' => $itemsParameters
                ]
                )
                ->setFrom($sender)
                ->addTo($customerEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
