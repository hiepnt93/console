<?php

namespace Vnecoms\Membership\Model\Source;

use Magento\Customer\Api\GroupManagementInterface;

class Group extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var GroupManagementInterface
     */
    protected $_groupManagement;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_converter;

    /**
     * @var \Vnecoms\Membership\Helper\Data
     */
    protected $_membershipHelper;

    /**
     * @param GroupManagementInterface              $groupManagement
     * @param \Magento\Framework\Convert\DataObject $converter
     */
    public function __construct(
        \Vnecoms\Membership\Helper\Data $membershipHelper,
        GroupManagementInterface $groupManagement,
        \Magento\Framework\Convert\DataObject $converter
    ) {
        $this->_membershipHelper = $membershipHelper;
        $this->_groupManagement = $groupManagement;
        $this->_converter = $converter;
    }

    /**
     * Get membership group ids.
     * 
     * @return array
     */
    public function getMembershipGroupIds()
    {
        return $this->_membershipHelper->getMembershipGroupIds();
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface::getAllOptions()
     */
    public function getAllOptions($blankLine = false)
    {
        if (!$this->_options) {
            $this->_options = [];
            $groups = $this->_groupManagement->getLoggedInGroups();
            foreach ($groups as $group) {
                /*Exclude default customer group id*/
                if (!in_array($group->getId(), $this->getMembershipGroupIds())) {
                    continue;
                }
                $this->_options[] = [
                    'value' => $group->getId(),
                    'label' => $group->getCode(),
                ];
            }
            if ($blankLine) {
                array_unshift($this->_options, ['value' => '', 'label' => __('-- Please Select --')]);
            }
        }

        return $this->_options;
    }

    /**
     * Retrieve option array.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->toOptionArray() as $option) {
            $_options[$option['value']] = $option['label'];
        }

        return $_options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
