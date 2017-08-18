<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */

namespace SITC\Sublogins\Controller\Adminhtml\Account;

class NewAction extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Create new customer action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('edit');
        return $resultForward;
    }
}