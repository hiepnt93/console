<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Membership\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Vnecoms\Membership\Model\Source\DurationUnit;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Vnecoms\Membership\Model\Product\Type\Membership as MembershipType;

class Membership extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Catalog product visibility.
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Vnecoms\Membership\Helper\Data
     */
    protected $_membershipHelper;

    /**
     * Constructor.
     * 
     * @param \Magento\Catalog\Block\Product\Context    $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver     $layerResolver
     * @param CategoryRepositoryInterface               $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data        $urlHelper
     * @param CollectionFactory                         $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param PriceCurrencyInterface                    $priceCurrency
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\Json\EncoderInterface  $jsonEncoder
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Vnecoms\Membership\Helper\Data $membershipHelper,
        array $data = []
    ) {
        $this->_localeFormat = $localeFormat;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->catalogConfig = $context->getCatalogConfig();
        $this->priceCurrency = $priceCurrency;
        $this->_membershipHelper = $membershipHelper;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $title = $this->_membershipHelper->getPageTitle();
        $this->pageConfig->getTitle()->set($title);
        $description = $this->_membershipHelper->getPageDescription();
        $this->pageConfig->setDescription($description);
        $keywords = $this->_membershipHelper->getPageKeywords();
        if ($keywords) {
            $this->pageConfig->setKeywords($keywords);
        }

        return $this;
    }

    protected function _beforeToHtml()
    {
        return $this;
    }

    /**
     * Retrieve loaded category collection.
     *
     * @return AbstractCollection
     */
    public function getMembershipCollection()
    {
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->_productCollectionFactory->create();
            $this->_productCollection->addAttributeToFilter('type_id', MembershipType::TYPE_CODE);
            $this->_productCollection->addAttributeToSelect($this->catalogConfig->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->setVisibility($this->productVisibility->getVisibleInCatalogIds())
                ->setOrder('membership_sort_order', 'ASC');
        }

        return $this->_productCollection;
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
     * Format price.
     *
     * @param int    $number
     * @param string $includeContainer
     */
    public function formatPrice($number, $includeContainer = false)
    {
        return $this->priceCurrency->format($number, $includeContainer);
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
        return $this->priceCurrency->convert($amount);
    }

    /**
     * Format price to base currency.
     *
     * @param number $amount
     *
     * @return string
     */
    public function formatBasePrice($amount = 0)
    {
        return $this->_storeManager->getStore()->getBaseCurrency()->formatPrecision($amount, 2, [], false);
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
}
