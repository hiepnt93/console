<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Membership\Model\ResourceModel\Transaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'transaction_id';

    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Membership\Model\Transaction', 'Vnecoms\Membership\Model\ResourceModel\Transaction');
    }
}
