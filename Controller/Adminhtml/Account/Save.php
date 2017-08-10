<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SITC\Sublogins\Controller\Adminhtml\Account;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Controller\Adminhtml\Index;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Customer\Model\Metadata\Form;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Customer\Controller\Adminhtml\Index\Save
{
    public function execute()
    {
        $response = parent::execute();
        $returnToEdit = false;
        $customerId = $this->getCurrentCustomerId();
        if ($returnToEdit) {
            if ($customerId) {
                $response->setPath(
                    'sublogins/account/edit',
                    ['id' => $customerId, '_current' => true]
                );
            } else {
                $response->setPath(
                    'sublogins/account/new',
                    ['_current' => true]
                );
            }
        } else {
            $response->setPath('sublogins/account/index');
        }
        return $response;
    }
    private function getCurrentCustomerId()
    {
        $originalRequestData = $this->getRequest()->getPostValue(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER);

        $customerId = isset($originalRequestData['entity_id'])
            ? $originalRequestData['entity_id']
            : null;

        return $customerId;
    }
}
