<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Membership\Controller\Adminhtml\Account;

use Vnecoms\Membership\Controller\Adminhtml\Action;

class Index extends Action
{
    /**
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Manage Membership Accounts'), __('Manage Membership Accounts'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Membership Accounts'));
        $this->_view->renderLayout();
    }
}
