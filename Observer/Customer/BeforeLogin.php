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
        if(!empty($expireDate)){
            $expireDate = $expireDate->getValue();
        }
        $isSubAccount = $customer->getCustomAttribute('is_sub_login');
        if(!empty($isSubAccount)){
            $isSubAccount = $isSubAccount->getValue();
        }
        $statusAccount = $customer->getCustomAttribute('is_active_sublogin');
        if(!empty($statusAccount)){
            $statusAccount = $statusAccount->getValue();
        }
        if ($customer && !empty($expireDate) && $isSubAccount == 1) {
            $expireDate = new \DateTime($expireDate);
            $today = new \DateTime('today');
            if ($expireDate < $today){
                throw new LocalizedException(__('Your account had been expired date. You can not login to our site.'));
            }
        }elseif($statusAccount == 0 && $isSubAccount == 1) {
            throw new LocalizedException(__('Your account is Deactive.'));
        }
    }


}
