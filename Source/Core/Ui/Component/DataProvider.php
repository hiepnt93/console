<?php

namespace Vnecoms\Membership\Ui\Component;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var \Vnecoms\Membership\Helper\Data
     */
    protected $_membershipHelper;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Vnecoms\Membership\Helper\Data $membershipHelper,
        array $meta = [],
        array $data = []
    ) {
        $this->_membershipHelper = $membershipHelper;

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->addFilter(
            $this->filterBuilder->setField('group_id')
                ->setValue($this->_membershipHelper->getMembershipGroupIds())
                ->setConditionType('in')->create()
        );
    }
}
