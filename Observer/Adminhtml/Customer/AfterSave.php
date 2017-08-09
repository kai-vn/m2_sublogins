<?php

namespace SITC\Sublogins\Observer\Adminhtml\Customer;

use Magento\Framework\Event\ObserverInterface;

class AfterSave implements ObserverInterface
{
    protected $_customerRepository;
    protected $_encryptor;

    /**
     * @var CustomerRegistry
     */
    private $_customerRegistry;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    )
    {
        $this->_customerRepository = $customerRepository;
        $this->_encryptor = $encryptor;
        $this->_customerRegistry = $customerRegistry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getCustomer();
        $requestParams = $observer->getEvent()->getRequest()->getParams('customer');

        if (!empty($requestParams['customer']['password_confirmation']) && !empty($requestParams['customer']['password_hash'])
            && $requestParams['customer']['password_confirmation'] === $requestParams['customer']['password_hash']) {
            $customerSecure = $this->_customerRegistry->retrieveSecureData($customer->getId());

            $passwordHash = $this->_encryptor->getHash($requestParams['customer']['password_hash'], true);

            $customerSecure->setPasswordHash();
            $customerSecure->setRpToken(null);
            $customerSecure->setRpTokenCreatedAt(null);
            $customerSecure->setPasswordHash($passwordHash);

            $this->_customerRepository->save($customer, $passwordHash);
        }
    }
}