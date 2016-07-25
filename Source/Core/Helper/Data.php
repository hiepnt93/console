<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Membership\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const XML_DEFAULT_CUSTOMER_GROUP = 'customer/create_account/default_group';
    const XML_MEMBERSHIP_GROUP = 'membership/general/membership_group';
    const XML_MEMBERSHIP_PAGE_TITLE = 'membership/membership_page/page_title';
    const XML_MEMBERSHIP_PAGE_KEYWORDS = 'membership/membership_page/meta_keyword';
    const XML_MEMBERSHIP_PAGE_DESCRIPTION = 'membership/membership_page/meta_description';
    const XML_MEMBERSHIP_COLOR_PACKAGE = 'membership/color/package';

    /**
     * Get Default customer group Id.
     * 
     * @return \Magento\Framework\App\Config\mixed
     */
    public function getDefaultCustomerGroupId()
    {
        return $this->scopeConfig->getValue(self::XML_DEFAULT_CUSTOMER_GROUP);
    }

    /**
     * Get membership group ids.
     * 
     * @return array
     */
    public function getMembershipGroupIds()
    {
        $groups = $this->scopeConfig->getValue(self::XML_MEMBERSHIP_GROUP);
        if (!$groups) {
            return [];
        }

        $ids = [];
        $groups = unserialize($groups);
        foreach ($groups as $group) {
            $ids[] = $group['group'];
        }

        return $ids;
    }

    /**
     * Get membership groups.
     * 
     * @return array
     */
    public function getMembershipGroups()
    {
        $groups = $this->scopeConfig->getValue(self::XML_MEMBERSHIP_GROUP);
        if (!$groups) {
            return [];
        }

        $result = [];
        $groups = unserialize($groups);
        foreach ($groups as $group) {
            $result[$group['group']] = $group['priority'];
        }

        return $result;
    }

    /**
     * Get Membership page title.
     *
     * @return string
     */
    public function getPageTitle()
    {
        return $this->scopeConfig->getValue(self::XML_MEMBERSHIP_PAGE_TITLE);
    }

    /**
     * Get Membership page title.
     *
     * @return string
     */
    public function getPageKeywords()
    {
        return $this->scopeConfig->getValue(self::XML_MEMBERSHIP_PAGE_KEYWORDS);
    }

    /**
     * Get Membership page title.
     *
     * @return string
     */
    public function getPageDescription()
    {
        return $this->scopeConfig->getValue(self::XML_MEMBERSHIP_PAGE_DESCRIPTION);
    }

    /**
     * Get Package Color.
     * 
     * @param int $package
     */
    public function getPackageColor($package)
    {
        return $this->scopeConfig->getValue(self::XML_MEMBERSHIP_COLOR_PACKAGE.$package);
    }
}
