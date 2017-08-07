<?php

namespace SITC\Sublogins\Plugin\Customer\Model\Customer;

use Magento\Customer\Model\Customer\DataProvider as CustomerDataProvider;
use Magento\Framework\App\ObjectManager;

class DataProvider
{
    protected $_request;
    protected $session;
    /**
     * Customer Repository.
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * Encryptor.
     *
     * @var \Magento\Framework\Encryption\Encryptor
     */
    protected $_encryptor;

    /**
     * Customer registry.
     *
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $_customerRegistry;
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
          \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    )
    {
        $this->_customerRepository = $customerRepository;
        $this->_encryptor          = $encryptor;
        $this->_customerRegistry   = $customerRegistry;
        $this->_request = $request;
    }

    public function afterGetData(CustomerDataProvider $subject, $result)
    {

        $subLoginParentId = $this->_request->getParam('sub_parent_id');
        if (!empty($subLoginParentId)) {
            $this->getSession()->setSubParentId($subLoginParentId);
        }
        return $result;
    }

    protected function getSession()
    {
        if ($this->session === null) {
            $this->session = ObjectManager::getInstance()->get(
                \Magento\Framework\Session\SessionManagerInterface::class
            );
        }
        return $this->session;
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

}
