<?php

namespace SITC\Sublogins\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Customer setup factory
     *
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    )
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $setup->startSetup();

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(Customer::ENTITY, 'can_create_sub_login', [
            'type' => 'int',
            'label' => 'Can Create Sub Account',
            'input' => 'boolean',
            'backend' => 'Magento\Customer\Model\Attribute\Backend\Data\Boolean',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'position' => 999,
            'system' => false,
            'sort_order' => 900
        ]);

        $customerSetup->addAttribute(Customer::ENTITY, 'max_sub_logins', [
            'type' => 'varchar',
            'label' => 'Max Sub Accounts',
            'input' => 'text',
            'sort_order' => 900,
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'position' => 999,
            'system' => false,
        ]);

        $customerSetup->addAttribute(Customer::ENTITY, 'expire_date', [
            'type' => 'datetime',
            'label' => 'Expire Date',
            'input' => 'date',
            'frontend' => 'Magento\Eav\Model\Entity\Attribute\Frontend\Datetime',
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\Datetime',
            'required' => false,
            'sort_order' => 900,
            'visible' => true,
            'system' => false,
            'user_defined' => true,
            'position' => 999
        ]);

        $customerSetup->addAttribute(Customer::ENTITY, 'sublogin_parent_id', [
            'type' => 'int',
            'label' => 'Parent ID',
            'required' => false,
            'visible' => false,
            'user_defined' => true,
            'position' => 999,
            'system' => false,
            'sort_order' => 900,
            'is_filterable_in_grid' => true
        ]);

        $customerSetup->addAttribute(Customer::ENTITY, 'is_sub_login', [
            'type' => 'int',
            'label' => 'Is Sub Account',
            'required' => false,
            'visible' => false,
            'user_defined' => true,
            'position' => 999,
            'system' => false,
            'sort_order' => 900,
            'is_used_in_grid' => true,
            'is_filterable_in_grid' => true,
            'default' => \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_NOT_SUB_LOGIN,
            'default_value' => \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_NOT_SUB_LOGIN
        ]);

        $customerSetup->addAttribute(Customer::ENTITY, 'is_active_sublogin', [
            'type' => 'int',
            'label' => 'Is Active Sub Account',
            'required' => false,
            'visible' => false,
            'user_defined' => true,
            'position' => 999,
            'system' => false,
            'sort_order' => 900,
            'is_filterable_in_grid' => true
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'can_create_sub_login')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer']
            ]);
        $attribute->save();

        $maxAttribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'max_sub_logins')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer'],
            ]);
        $maxAttribute->save();

        $exAttribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'expire_date')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer']
            ]);
        $exAttribute->save();

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'sublogin_parent_id')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer']
            ]);
        $attribute->save();

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'is_active_sublogin')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer']
            ]);
        $attribute->save();

        $isSubLoginAttribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'is_sub_login')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer']
            ]);
        $isSubLoginAttribute->save();

        $setup->endSetup();

        $this->updateCustomerAttributes($setup, $isSubLoginAttribute);
    }

    public function updateCustomerAttributes(ModuleDataSetupInterface $setup, $isSubLoginAttribute)
    {
        $connection = $setup->getConnection();

        $customerEntityTable = $connection->getTableName('customer_entity');
        $customerEntityIntTable = $connection->getTableName('customer_entity_int');
        $columns = [
            'attribute_id' => new \Zend_Db_Expr($isSubLoginAttribute->getId()),
            'entity_id' => 'entity_id',
            'value' => new \Zend_Db_Expr(\SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_NOT_SUB_LOGIN)
        ];

        $select = $connection->select()->from(
            [$customerEntityTable],
            $columns
        );
        $query = $select->insertFromSelect($customerEntityIntTable, array_keys($columns));

        $connection->query($query);
    }
}