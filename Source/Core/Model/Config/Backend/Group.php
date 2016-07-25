<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Membership\Model\Config\Backend;

class Group extends \Magento\Framework\App\Config\Value
{
    /**
     * @return $this
     */
    public function beforeSave()
    {
        if (is_array($this->getValue())) {
            $data = [];
            $addedGroupIds = [];
            foreach ($this->getValue() as $value) {
                if (isset($value['delete']) && $value['delete']) {
                    continue;
                }
                if (in_array($value['group'], $addedGroupIds)) {
                    throw new \Exception(__('Group #%1 is added already', $value['group']));
                }
                $addedGroupIds[] = $value['group'];
                $data[$value['priority']] = [
                    'group' => $value['group'],
                    'priority' => $value['priority'],
                ];
            }

            ksort($data);
            $this->setValue(serialize($data));
        }

        return parent::beforeSave();
    }
}
