<?php

namespace Namespace\Module\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class ObserverName
 * @package <Module>\Observer
 */
class ObserverName implements ObserverInterface
{
    /**
     * Add the notification if there are any vendor awaiting for approval. 
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //content of observer
    }
}
