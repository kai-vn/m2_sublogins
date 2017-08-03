<?php

namespace SITC\Sublogins\Api;

/**
 * Interface for managing customers accounts.
 * @api
 */
interface AccountManagementInterface
{
    /**#@+
     * Constant for confirmation status
     */
    const MAX_PASSWORD_LENGTH = 256;
    /**#@-*/

    /**
     * Create customer account. Perform necessary business operations like sending email.
     *
     * @param \SITC\Sublogins\Api\Data\CustomerInterface $customer
     * @param string $password
     * @param string $redirectUrl
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAccount(
        \SITC\Sublogins\Api\Data\CustomerInterface $customer,
        $password = null,
        $redirectUrl = ''
    );
}
