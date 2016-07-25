<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Membership\Controller\Adminhtml\Product;

use Vnecoms\Membership\Controller\Adminhtml\Action;

class Index extends Action
{
    /**
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Manage Membership Products'), __('Manage Membership Products'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Membership Products'));
        $this->_view->renderLayout();
    }
}
