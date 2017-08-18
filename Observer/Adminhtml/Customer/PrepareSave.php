<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */
namespace SITC\Sublogins\Observer\Adminhtml\Customer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class PrepareSave implements ObserverInterface
{
    protected $session;
    protected $helper;
    protected $customerFactory;

    public function __construct(
        \SITC\Sublogins\Helper\Data $helper,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        $this->helper = $helper;
        $this->customerFactory = $customerFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $parentId = $this->getSession()->getSubParentId();
        $customer = $observer->getCustomer();
        if (!empty($parentId)) {
            $parent = $this->customerFactory->create()->load($parentId);
            if (!$customer->getId()) {
                $countSubAccounts = $this->helper->getCountSubAccounts($parentId);
                $maxSubAccounts = $parent->getMaxSubLogins();
                if ($maxSubAccounts && $countSubAccounts + 1 > $maxSubAccounts) {
                    $this->getSession()->unsSubParentId();
                    throw new LocalizedException(__('You cannot create more than %1 sub accounts for this customer.', $maxSubAccounts));
                }
            }
            $requestParams = $observer->getEvent()->getRequest()->getParams('customer');
            if (!$customer->getId() && (empty($requestParams['customer']['password_hash']) || empty($requestParams['customer']['password_confirmation']))) {
                $this->getSession()->unsSubParentId();
                throw new LocalizedException(__('Please enter the customer password.'));
            }

            if (!empty($requestParams['customer']['password_confirmation']) && !empty($requestParams['customer']['password_hash'])
                && $requestParams['customer']['password_confirmation'] !== $requestParams['customer']['password_hash']) {
                $this->getSession()->unsSubParentId();
                throw new LocalizedException(__('Please make sure your passwords match.'));
            }
            $customer->setCustomAttribute('sublogin_parent_id', $parentId);
            $customer->setCustomAttribute('is_sub_login', \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_SUB_LOGIN);
            $customer->setCustomAttribute('is_active_sublogin', $requestParams['customer']['is_active_sublogin']);
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