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
        $subloginParentId = $this->getSession()->getSubParentId();
        $customer = $observer->getCustomer();
        $request = $observer->getRequest()->getPostValue('customer');
        if (!empty($subloginParentId)) {
            if($request['max_sub_logins']) {
                $customer->setCustomAttribute('max_sub_logins', $request['max_sub_logins']);
            }
            $collectionSize = $this->collectionFactory->create()
                ->addAttributeToSelect(['sublogin_parent_id', 'is_sub_login'])
                ->addAttributeToFilter('is_sub_login', \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_SUB_LOGIN)
                ->addAttributeToFilter('sublogin_parent_id', $subloginParentId)->getSize();
            $countSublogins = (int)$collectionSize + 1;
            $maxSublogins = $this->_customerRepositoryInterface->getById($subloginParentId)->getCustomAttribute('max_sub_logins')->getValue();
            if($countSublogins > $maxSublogins) {
                $this->getSession()->unsSubParentId();
                throw new LocalizedException(__('You just can create ' . $maxSublogins . '.'));
            }
            $customer->setCustomAttribute('sublogin_parent_id', $subloginParentId);
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