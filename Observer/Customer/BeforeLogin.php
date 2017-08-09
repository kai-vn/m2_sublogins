<?php

namespace SITC\Sublogins\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class BeforeLogin implements ObserverInterface
{
    protected $customerRepository;
    protected $messageManager;
    protected $accountRedirect;
    protected $storeManager;
    protected $request;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->customerRepository = $customerRepository;
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $login = $this->request->getPost('login');
        $customer = $this->customerRepository->get($login['username'], $this->storeManager->getStore()->getWebsiteId());

        $expireDate = $customer->getCustomAttribute('expire_date');
        if (!empty($expireDate)) {
            $expireDate = $expireDate->getValue();
        }

        $isSubAccount = $customer->getCustomAttribute('is_sub_login');
        if (!empty($isSubAccount)) {
            $isSubAccount = $isSubAccount->getValue();
        }

        $statusAccount = $customer->getCustomAttribute('is_active_sublogin');
        if (!empty($statusAccount)) {
            $statusAccount = $statusAccount->getValue();
        }

        if ($customer && !empty($expireDate) && $isSubAccount == \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_SUB_LOGIN) {
            $expireDate = new \DateTime($expireDate);
            $today = new \DateTime('today');
            if ($expireDate < $today) {
                throw new LocalizedException(__('Your account has been expired. Please contact to us for further information.'));
            }
        }

        if ($statusAccount == 0 && $isSubAccount == \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_SUB_LOGIN) {
            throw new LocalizedException(__('Your account is currently not available. Please contact to us for further information.'));
        }
    }
}
