<?php

namespace SITC\Sublogins\Observer\Adminhtml\Customer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;

class PrepareSave implements ObserverInterface
{
    protected $_coreRegistry = null;
    protected $session;
    protected $helper;
    protected $_customerSession;
    protected $customerFactory;
    protected $customerDataFactory;
    protected $collectionFactory;
    protected $_customerRepositoryInterface;

    public function __construct(
        \SITC\Sublogins\Helper\Data $helper,
        CustomerSession $customerSession,
        CustomerInterfaceFactory $customerDataFactory,
        CollectionFactory $collectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->customerFactory = $customerFactory;
        $this->_customerSession = $customerSession;
        $this->helper = $helper;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerDataFactory = $customerDataFactory;
        $this->_coreRegistry = $coreRegistry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $parentId = $this->getSession()->getSubParentId();
        $customer = $observer->getCustomer();
        if (!empty($parentId)) {
            $parent = $this->customerFactory->create()->load($parentId);
            $countSubAccounts = $this->helper->getCountSubAccounts($parentId);
            $maxSubAccounts = $parent->getMaxSubLogins();
            if($maxSubAccounts && $countSubAccounts + 1 > $maxSubAccounts) {
                $this->getSession()->unsSubParentId();
                throw new LocalizedException(__('You cannot create more than %1 sub accounts for this customer.', $maxSubAccounts));
            }
            $customer->setCustomAttribute('sublogin_parent_id', $parentId);
            $customer->setCustomAttribute('is_sub_login',  \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_SUB_LOGIN);
        }
        $this->getSession()->unsSubParentId();
        return $customer;
    }

    protected function getSession()
    {
        if ($this->session === null) {
            $this->session = ObjectManager::getInstance()->get(\Magento\Framework\Session\SessionManagerInterface::class);
        }
        return $this->session;
    }
}