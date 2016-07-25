<?php

namespace Vnecoms\Membership\Block\Adminhtml\System\Config\Form\Field;

class Group extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $block = $this->getLayout()->createBlock('Vnecoms\Membership\Block\Adminhtml\System\Config\Form\Field\Group\Field');
        $block->setElement($element);

        return $block->toHtml();
    }
}
