<?php

namespace Vnecoms\Membership\Block\Customer;

use Vnecoms\Membership\Model\ResourceModel\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Vnecoms\Membership\Model\Source\DurationUnit;
use Vnecoms\Membership\Model\Product\Type\Membership as MembershipType;

class Membership extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Vnecoms\Membership\Helper\Data
     */
    protected $_membershipHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\GroupRegistry
     */
    protected $_groupRegistry;

    /**
     * @var \Vnecoms\Membership\Model\ResourceModel\Transaction\Collection
     */
    protected $_transCollection;

    /**
     * @var Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * Constructor.
     * 
     * @param \Vnecoms\Membership\Helper\Data                  $membershipHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Vnecoms\Membership\Helper\Data $membershipHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\GroupRegistry $groupRegistry,
        TransactionCollectionFactory $collectionFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_membershipHelper = $membershipHelper;
        $this->_groupRegistry = $groupRegistry;
        $this->_priceCurrency = $priceCurrency;
        $this->_productFactory = $productFactory;
        $this->urlHelper = $urlHelper;
        $this->_cartHelper = $cartHelper;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_localeFormat = $localeFormat;

        $this->_transCollection = $collectionFactory->create();
        $this->_transCollection
            ->addFieldToFilter('customer_id', $this->getCustomer()->getId())
            ->addOrder('created_at', 'DESC');

        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('My Membership'));
        /*
         * @var \Magento\Theme\Block\Html\Pager
         * Show page
        */
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'membership.hítory.pager'
        );
        $pager->setLimit(20)
            ->setCollection($this->_transCollection);
        $this->setChild('pager', $pager);

        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Is Membership.
     * 
     * @return bool
     */
    public function isMembership()
    {
        $membershipGroupIds = $this->_membershipHelper->getMembershipGroupIds();
        $groupId = $this->getCustomer()->getGroupId();

        return in_array($groupId, $membershipGroupIds);
    }
    /**
     * Get current customer.
     * 
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    /**
     * Get current Group Name.
     * 
     * @return string
     */
    public function getCurrentGroupName()
    {
        $groupId = $this->getCustomer()->getGroupId();

        return $this->_groupRegistry->retrieve($groupId)->getCode();
    }

    /**
     * Get expiry date.
     * 
     * @return string
     */
    public function getExpiryDate()
    {
        return $this->formatDate(
            $this->getCustomer()->getExpiryDate(),
            \IntlDateFormatter::MEDIUM
        );
    }

    /**
     * Get customer membership URL.
     * 
     * @return string
     */
    public function getCustomerMembershipUrl()
    {
        return $this->getUrl('membership/customer');
    }

    /**
     * Get payment history.
     * 
     * @return \Vnecoms\Membership\Model\ResourceModel\Transaction\Collection
     */
    public function getPaymentHistory()
    {
        return $this->_transCollection;
    }

    /**
     * Get duration label.
     *
     * @param int $duration
     * @param int $unit
     */
    public function getDurationLabel($duration, $unit)
    {
        $label = '';
        switch ($unit) {
            case DurationUnit::DURATION_DAY:
                $label = $duration == 1 ? __('%1 Day', $duration) : __('%1 Days', $duration);
                break;
            case DurationUnit::DURATION_WEEK:
                $label = $duration == 1 ? __('%1 Week', $duration) : __('%1 Weeks', $duration);
                break;
            case DurationUnit::DURATION_MONTH:
                $label = $duration == 1 ? __('%1 Month', $duration) : __('%1 Months', $duration);
                break;
            case DurationUnit::DURATION_YEAR:
                $label = $duration == 1 ? __('%1 Year', $duration) : __('%1 Years', $duration);
                break;
        }

        return $label;
    }

    /**
     * Format price.
     * 
     * @param string $price
     */
    public function formatPrice($price = 0)
    {
        $price = $this->_priceCurrency->convert($price);

        return $this->_priceCurrency->format($price, false);
    }

    /**
     * Convert the base currency price to current currency.
     *
     * @param float $amount
     *
     * @return float
     */
    public function convertPrice($amount = 0)
    {
        return $this->_priceCurrency->convert($amount);
    }

    /**
     * Retrieve Product URL using UrlDataObject.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array                          $additional the route params
     *
     * @return string
     */
    public function getProductUrl($product, $additional = [])
    {
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }

            return $product->getUrlModel()->getUrl($product, $additional);
        }

        return '#';
    }

    /**
     * Retrieve url for add product to cart
     * Will return product view page URL if product has required options.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array                          $additional
     *
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        if ($product->getTypeInstance()->hasRequiredOptions($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            if (!isset($additional['_query'])) {
                $additional['_query'] = [];
            }
            $additional['_query']['options'] = 'cart';

            return $this->getProductUrl($product, $additional);
        }

        return $this->_cartHelper->getAddUrl($product, $additional);
    }

    /**
     * Get post parameters.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);

        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ],
        ];
    }

    /**
     * Get option JSON.
     *
     * @return string
     */
    public function getOptionsJSON(\Magento\Catalog\Model\Product $product)
    {
        $options = [];
        $durationOptions = $product->getData('membership_duration');
        if (is_array($durationOptions)) {
            foreach ($durationOptions as $option) {
                $options[] = [
                'label' => $this->getDurationLabel($option['duration'], $option['duration_unit']).' - '.$this->formatPrice($option['price']),
                'value' => $option['duration'].'|'.$option['duration_unit'],
                'price' => $this->convertPrice($option['price']),
            ];
            }
        }

        return $this->_jsonEncoder->encode($options);
    }

    /**
     * Get price format json.
     *
     * @return string
     */
    public function getPriceFormatJSON()
    {
        $priceFormat = $this->_localeFormat->getPriceFormat();

        return $this->_jsonEncoder->encode($priceFormat);
    }

    /**
     * Get current package product.
     * 
     * @return \Magento\Catalog\Model\Product
     */
    public function getCurrentPackageProduct()
    {
        $groupId = $this->getCustomer()->getGroupId();
        $product = $this->_productFactory->create();
        $collection = $product->getCollection()
            ->addAttributeToFilter('type_id', MembershipType::TYPE_CODE)
            ->addAttributeToFilter('membership_related_group_id', $groupId);
        $product = $collection->getFirstItem();
        $product->load($product->getId());

        return $product;
    }

    /**
     * Get membership pricing page URL.
     * 
     * @return string
     */
    public function getMembershipPricingUrl()
    {
        return $this->getUrl('membership');
    }
}
