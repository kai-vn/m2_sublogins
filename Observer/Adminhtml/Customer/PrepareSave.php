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
    protected $_customerRepository;
    protected $_encryptor;
    protected $_customerRegistry;
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
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->_customerRepository = $customerRepository;
        $this->_encryptor = $encryptor;
        $this->_customerRegistry   = $customerRegistry;
        $this->collectionFactory = $collectionFactory;
        $this->customerFactory = $customerFactory;
        $this->_customerSession = $customerSession;
        $this->helper = $helper;
        $this->encryptor = $encryptor;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerDataFactory = $customerDataFactory;
        $this->_coreRegistry = $coreRegistry;
    }
    public function beforeAuthenticate(\Magento\Customer\Model\AccountManagement $subject, ...$args)
    {
        if (!empty($args[0]) && !empty($args[1])) {
            try {
                $customer = $this->_customerRepository->get($args[0]);
                /* Logic for validation hash from old website here */
                $passwordHash = $this->_encryptor->getHash($args[1], true);
                $customerSecure = $this->_customerRegistry->retrieveSecureData($customer->getId());
                $customerSecure->setRpToken(null);
                $customerSecure->setRpTokenCreatedAt(null);
                $customerSecure->setPasswordHash($passwordHash);
                $this->_customerRepository->save($customer, $passwordHash);
                $this->_customerRegistry->remove($customer->getId());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                return $args;
            }
        }

        return $args;
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