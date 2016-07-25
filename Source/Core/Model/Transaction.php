<?php

namespace Vnecoms\Membership\Model;

class Transaction extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Membership\Model\ResourceModel\Transaction');
    }
}
