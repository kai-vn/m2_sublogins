<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */
namespace SITC\Sublogins\Helper;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\Session as CustomerSession;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_customerSession;

    protected $_customerFactory;

    protected $_customerCollectionFactory;

    protected $_subAccounts;

    /**
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CustomerCollectionFactory $customerCollectionFactory,
        CustomerSession $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_customerFactory = $customerFactory;

        $this->_resource = $resource;
        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }

    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }

    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    public function getCanViewOrder()
    {
        return $this->scopeConfig->getValue(
            'sublogins/general/can_view_order',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getId()
        );
    }

    public function getSubAccounts()
    {
        if (!$this->_subAccounts) {
            $customerId = $this->_customerSession->getCustomerId();
            $this->subAccounts = $this->_customerCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('is_sub_login', \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_SUB_LOGIN)
                ->addAttributeToFilter('sublogin_parent_id', $customerId);
        }
        return $this->_subAccounts;
    }

    public function getCountSubAccounts($customerId = null)
    {
        if (!$customerId) {
            $customerId = $this->_customerSession->getCustomerId();
            if (!$customerId) {
                return false;
            }
        }

        $connection = $this->_resource->getConnection();

        $select = $connection->select()
            ->from(
                ['main_table' => $connection->getTableName('customer_grid_flat')],
                [new \Zend_Db_Expr('COUNT(main_table.entity_id)')]
            )
            ->where('main_table.sublogin_parent_id = :customer_id');

        $bind = ['customer_id' => $customerId];
        return $connection->fetchOne($select, $bind);
    }

    public function isSublogin($customer = null)
    {
        if (!$customer) {
            $customer = $this->_customerSession->getCustomer();
        }

        if ($customer->getIsSubLogin() == \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_SUB_LOGIN) {
            return true;
        }

        return false;
    }

    public function getExpireDate($customer = null)
    {
        if (!$customer) {
            $customer = $this->_customerSession->getCustomer();
        }

        if ($customer->getExpireDate()) {
            return true;
        }

        return false;
    }

    public function getCreateSublogin($customer = null)
    {
        if (!$customer) {
            $customer = $this->_customerSession->getCustomer();
        }

        if ($customer->getCanCreateSubLogin()) {
            return true;
        }

        return false;
    }

    public function getSubRegisterPostUrl()
    {
        return $this->_getUrl('sublogins/account/createpost');
    }
}
