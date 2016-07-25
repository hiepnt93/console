<?php

namespace Vnecoms\Membership\Block\Membership;

class Head extends \Magento\Framework\View\Element\Template
{
    public function getCssUrl()
    {
        return $this->getUrl('membership/index/css');
    }
}
