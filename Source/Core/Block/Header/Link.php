<?php

namespace Vnecoms\Membership\Block\Header;

class Link extends \Magento\Framework\View\Element\Template
{
    /**
     * Get Membership Url.
     * 
     * @return string
     */
    public function getMembershipUrl()
    {
        return $this->getUrl('membership');
    }

    /**
     * Get Label.
     * 
     * @return string;
     */
    public function getLabel()
    {
        return __('Membership');
    }
}
