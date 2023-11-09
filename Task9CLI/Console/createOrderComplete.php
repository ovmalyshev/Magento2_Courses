<?php
namespace Speroteck\Task9CLI\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\State;
use Magento\Quote\Model\QuoteManagement;
use Magento\Catalog\Api\ProductRepositoryInterface;


class createOrderComplete extends Command
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var State
     */
    private $state;

    /**
     * @var QuoteManagement
     */
    private $quoteManagement;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        StoreManagerInterface $storeManager,
        QuoteFactory $quoteFactory,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        State $state,
        QuoteManagement $quoteManagement,
        ProductRepositoryInterface $productRepository,
        $name = null
    ) {
        $this->storeManager = $storeManager;
        $this->quoteFactory = $quoteFactory;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->state = $state;
        $this->quoteManagement = $quoteManagement;
        $this->productRepository = $productRepository;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('createOrder:complete')
            ->setDescription('Creates a new order');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        try {
            // Set customer data
            $enteredData = [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'email' => '5customer@example.com'
            ];

            // Set quantity
            $quantity = 2;

            // Set the products to be added to the quote
            $sku = '24-MB01';

            // Set the billing and shippind data
            $billShipData = [
                'firstname' => 'John',
                'lastname' => 'Doe',
                'street' => '123 Main St',
                'city' => 'City',
                'postcode' => '12345',
                'telephone' => '1234567890',
                'country_id' => 'US',
                'region_id' => '15',
            ];

            // Set the shipping and payment method
            $shippingMethod = 'flatrate_flatrate';
            $paymentMethod = 'custompayment';

            //main code
            $output->writeln('Creating order...');

            // Set the store ID where the order will be placed
            $storeId = $this->storeManager->getStore()->getId();

            // Create a new customer or load an existing customer
            $customer = $this->customerFactory->create();

            $customer->setData($enteredData);

            try {
                $customer = $this->customerRepository->get($enteredData['email']);
            } catch (\Exception $e) {
                $customer->save();
                $customer= $this->customerRepository->getById($customer->getEntityId());
                $output->writeln('New customer with email: ' . $enteredData['email'] . ' created');
            }

            // Create a new quote
            $quote = $this->quoteFactory->create();
            $quote->setStoreId($storeId);
            $quote->assignCustomer($customer);

            $product = $this->productRepository->get($sku);
            $quote->addProduct($product, $quantity);

            // Set the billing and shipping address
            $billingAddress = $quote->getBillingAddress()->addData($billShipData);
            $shippingAddress = $quote->getShippingAddress()->addData($billShipData);

            $shippingAddress->setCollectShippingRates(true)
                ->collectShippingRates()
                ->setShippingMethod($shippingMethod);

            $quote->setPaymentMethod($paymentMethod)
                ->setInventoryProcessed(false)
                ->save();

            // Place the order
            $quote->getPayment()->importData(['method' => $paymentMethod]);
            $quote->collectTotals()->save();
            $this->quoteManagement->submit($quote);

            $output->writeln('Order created successfully.');
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }
}

