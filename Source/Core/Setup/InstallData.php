<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Membership\Setup;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Vnecoms\Membership\Model\Product\Type\Membership;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $_categorySetupFactory;

    /**
     * Customer setup factory.
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * Init.
     * 
     * @param \Magento\Catalog\Setup\CategorySetupFactory  $categorySetupFactory
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
    ) {
        $this->_categorySetupFactory = $categorySetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $categorySetup = $this->_categorySetupFactory->create(
            ['setup' => $setup]
        );
        $setup->startSetup();

        $categorySetup->addAttribute(
            Product::ENTITY,
            'membership_related_group_id',
            [
                'group' => 'Membership Information',
                'label' => 'Related Customer Group',
                'type' => 'int',
                'input' => 'select',
                'position' => 10,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'source' => 'Vnecoms\Membership\Model\Source\Group',
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to' => Membership::TYPE_CODE,
                'note' => 'You need to add all membership groups from configuration first (Membership -> Settings)',
            ]
        );
        $categorySetup->addAttribute(
            Product::ENTITY,
            'membership_duration',
            [
                'group' => 'Membership Information',
                'label' => 'Duration',
                'type' => 'text',
                'input' => 'text',
                'position' => 20,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'backend' => 'Vnecoms\Membership\Model\Product\Attribute\Backend\Duration',
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to' => Membership::TYPE_CODE,
            ]
        );

        $categorySetup->addAttribute(
            Product::ENTITY,
            'membership_feature',
            [
                'group' => 'Membership Information',
                'label' => 'Is Featured Package',
                'type' => 'int',
                'input' => 'boolean',
                'position' => 30,
                'visible' => true,
                'default' => '0',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to' => Membership::TYPE_CODE,
            ]
        );

        $categorySetup->addAttribute(
            Product::ENTITY,
            'membership_sort_order',
            [
                'group' => 'Membership Information',
                'label' => 'Sort Order',
                'type' => 'int',
                'input' => 'text',
                'position' => 40,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'frontend_class' => 'validate-digits',
                'apply_to' => Membership::TYPE_CODE,
            ]
        );

        /*make sure these attributes are applied for membership product type only*/
        $attributes = [
            'membership_related_group_id',
            'membership_duration',
            'membership_feature',
            'membership_sort_order',
        ];
        foreach ($attributes as $attributeCode) {
            $attribute = $categorySetup->getAttribute(Product::ENTITY, $attributeCode);
            $categorySetup->updateAttribute(Product::ENTITY, $attributeCode, 'apply_to', Membership::TYPE_CODE);
        }

        $fieldList = [
            'tax_class_id',
        ];

        // make these attributes applicable to downloadable products
        foreach ($fieldList as $field) {
            $applyTo = explode(
                ',',
                $categorySetup->getAttribute(Product::ENTITY, $field, 'apply_to')
            );
            if (!in_array('membership', $applyTo)) {
                $applyTo[] = 'membership';
                $categorySetup->updateAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $field,
                    'apply_to',
                    implode(',', $applyTo)
                );
            }
        }

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(Customer::ENTITY, 'expiry_date', [
            'type' => 'static',
            'label' => 'Expiry Date',
            'input' => 'date',
            'required' => false,
            'visible' => true,
            'user_defined' => false,
            'sort_order' => 10,
            'position' => 10,
            'used_in_grid' => true,
            'visible_in_grid' => true,
            'searchable_in_grid' => true,
            'filterable_in_grid' => true,
            'system' => 0,
        ]);

        $expiryDateAttribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'expiry_date');
        $expiryDateAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );
        $expiryDateAttribute->save();

        $setup->endSetup();
    }
}
