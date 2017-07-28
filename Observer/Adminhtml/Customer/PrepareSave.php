<?php

namespace SITC\Sublogins\Observer\Adminhtml\Customer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;

class PrepareSave implements ObserverInterface
{
    protected $_coreRegistry = null;
    protected $session;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->_coreRegistry = $coreRegistry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $subloginParentId = $this->getSession()->getSubParentId();
        $customer = $observer->getCustomer();
        if (!empty($subloginParentId)) {
            $customer->setCustomAttribute('sublogin_parent_id', $subloginParentId);
            $customer->setCustomAttribute('is_sub_login', 1);
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