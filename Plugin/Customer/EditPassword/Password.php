<?php

namespace SITC\Sublogins\Plugin\Customer\EditPassword;


class Password
{
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

    /**
     * AccountManagementPlugin constructor.
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Encryption\Encryptor $encryptor
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     */
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

    /**
     * Authenticate Customer by Hash from Old site and update info in DB.
     *
     * @param \Magento\Customer\Model\AccountManagement $subject
     * @param array $args
     *
     * @return array
     */
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