<?php
/**
 * @copyright Copyright (c) 2016 www.tigren.com
 */

namespace SITC\Sublogins\Controller\Adminhtml\Account;

class Listing extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer orders grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();

        return $resultLayout;
    }
}
