<?php
namespace Speroteck\Task9CLI\Console;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\State;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class createOrderUseParams extends Command
{
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';
    const EMAIL = 'email';
    const STREET = 'street';
    const CITY = 'city';
    const POSTCODE = 'postcode';
    const TELEPHONE = 'telephone';
    const COUNTRY_ID = 'country_id';
    const REGION_ID= 'region_id';
    const ITEMS = 'items';
    const SHIPPING_METHOD = 'shippingMethod';
    const PAYMENT_METHOD = 'paymentMethod';

    /**
     * @var array
     */
    private $adressParameters = [
        self::FIRSTNAME => 'firstname',
        self::LASTNAME => 'lastname',
        self::EMAIL => 'email',
        self::STREET => 'street',
        self::CITY => 'city',
        self::POSTCODE => 'postcode',
        self::TELEPHONE => 'telephone',
        self::COUNTRY_ID => 'country_id',
        self::REGION_ID=> 'region_id',
    ];

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

    /**
     * @var AddressFactory
     */
    protected $_addressFactory;

    public function __construct(
        StoreManagerInterface $storeManager,
        QuoteFactory $quoteFactory,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        State $state,
        QuoteManagement $quoteManagement,
        ProductRepositoryInterface $productRepository,
        AddressFactory $addressFactory,
        $name = null
    ) {
        $this->storeManager = $storeManager;
        $this->quoteFactory = $quoteFactory;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        parent::__construct($name);

        $this->state = $state;
        $this->quoteManagement = $quoteManagement;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;

        $this->_addressFactory = $addressFactory;
    }

    protected function configure()
    {
        $this->setName('createOrder:setParams')
            ->setDescription('Creates a new order with parameters')
            ->setDefinition($this->getOptionList());
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        try {
            $firstname = $input->getOption(self::FIRSTNAME);
            $lastname = $input->getOption(self::LASTNAME);
            $email = $input->getOption(self::EMAIL);
            $shippingMethod = $input->getOption(self::SHIPPING_METHOD);
            $paymentMethod = $input->getOption(self::PAYMENT_METHOD);

            $adressData = $this->inputAdressParameters($input);

            $items = $this->orderSKU($input->getOption(self::ITEMS));

            $customerData = [
                self::FIRSTNAME => $firstname,
                self::LASTNAME => $lastname,
                self::EMAIL => $email
            ];

            //main code
            $output->writeln('Creating order...');
            $storeId = $this->storeManager->getStore()->getId();

            $customer = $this->customerFactory->create();
            $customer->setData($customerData);

            // Create a new quote
            $quote = $this->quoteFactory->create();
            $quote->setStoreId($storeId);

            try {
                $customer = $this->customerRepository->get($customerData['email']);
                $quote->assignCustomer($customer);
            } catch (\Exception $e) {
                $output->writeln('Creating order for Guest user');

                $quote->setCustomerFirstname("Guest First Name");
                $quote->setCustomerLastname("Guest Last Name");
                $quote->setCustomerEmail($email);
                $quote->setCustomerIsGuest(true);
            }

            //add items in quote
            foreach ($items as $item) {
                $qty = $item['qty'] ?: 1;
                try {
                    $product=$this->productRepository->get($item['sku']);
                    $quote->addProduct($product, intval($qty));
                } catch (\Exception $e) {
                    $output->writeln('Product with sku: ' . $item['sku'] . 'not exist and not added to order');
                }
            }

            // Set the billing and shipping address
            $shippingAddressId = $customer->getDefaultShipping();
            $billingAddressId = $customer->getDefaultBilling();
            if (!$shippingAddressId || !$billingAddressId) {
                if (!$adressData) {
                    return $output->writeln('adress data ' . $adressData[0] . 'is empty');
                }
                $shippingAddress = $quote->getShippingAddress()->addData($adressData);
                $billingAddress = $quote->getBillingAddress()->addData($adressData);
            } else {
                $shippingAddressData = $this->_addressFactory->create()->load($shippingAddressId);
                $billingAddressData = $this->_addressFactory->create()->load($billingAddressId);

                $shippingAddress = $quote->getShippingAddress()->addData($shippingAddressData->getData());
                $billingAddress = $quote->getBillingAddress()->addData($billingAddressData->getData());
            }

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

    /**
     * Get list of options for the command
     *
     * @return InputOption[]
     */
    private function getOptionList()
    {
        return [
            new InputOption(
                self::EMAIL,
                'e',
                InputOption::VALUE_REQUIRED,
                __("Customer email")
            ),
            new InputOption(
                self::FIRSTNAME,
                'fn',
                InputOption::VALUE_OPTIONAL,
                __("Customer first name"),
                'Guest First Name'
            ),
            new InputOption(
                self::LASTNAME,
                'ln',
                InputOption::VALUE_OPTIONAL,
                __("Customer last name"),
                'Guest Last Name'
            ),
            new InputOption(
                self::STREET,
                's',
                InputOption::VALUE_OPTIONAL,
                __("Adress data: street"),
                null
            ),
            new InputOption(
                self::CITY,
                'c',
                InputOption::VALUE_OPTIONAL,
                __("Adress data: city"),
                null
            ),
            new InputOption(
                self::POSTCODE,
                'p',
                InputOption::VALUE_OPTIONAL,
                __("Adress data: postcode"),
                null
            ),
            new InputOption(
                self::TELEPHONE,
                't',
                InputOption::VALUE_OPTIONAL,
                __("Adress data: telephone"),
                null
            ),
            new InputOption(
                self::COUNTRY_ID,
                'cid',
                InputOption::VALUE_OPTIONAL,
                __("Adress data: country_id"),
                null
            ),
            new InputOption(
                self::REGION_ID,
                'rid',
                InputOption::VALUE_OPTIONAL,
                __("Adress data: region_id"),
                null
            ),
            new InputOption(
                self::SHIPPING_METHOD,
                null,
                InputOption::VALUE_OPTIONAL,
                __("Shipping method"),
                'flatrate_flatrate'
            ),
            new InputOption(
                self::PAYMENT_METHOD,
                null,
                InputOption::VALUE_OPTIONAL,
                __("Payment method"),
                'custompayment'
            ),
            new InputOption(
                self::ITEMS,
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                __("Items in format '/SKU/quantity'/")
            )
        ];
    }

    /**
     * Get array of items and value
     *
     * @return array
     */
    public function orderSKU($itemsArray): array
    {
        $items = [];
        foreach ($itemsArray as &$value) {
            $curItem = [];
            $curItem['sku'] = explode("/", $value)[0];
            $curItem['qty'] = intval(explode("/", $value)[1]);
            array_push($items, $curItem);
        }
        return $items;
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    private function inputAdressParameters(InputInterface $input)
    {
        $parameters = [];
        foreach ($this->adressParameters as $adressFieldName => $adressValue) {
            if ($adressValue == null) {
                return [$adressFieldName];
            }
            $parameters[$adressValue] = $input->getOption($adressFieldName);
        }

        return $parameters;
    }
}
