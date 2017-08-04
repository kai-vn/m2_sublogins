<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SITC\Sublogins\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Active extends \Magento\Customer\Controller\AbstractAccount
{
    protected $customerFactory;
    protected $url;
    protected $request;
    protected $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->request = $request;
        $this->pageFactory = $pageFactory;
        $this->url = $url;
        $this->customerFactory = $customerFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerId = $this->request->getParam('ac');
        $customer = $this->customerFactory->create()->load($customerId);
        $customerData = $customer->getDataModel();
        $customerData->setCustomAttribute('is_active_sublogin', 1);
        $customer->updateData($customerData);
        $customer->save();
        return $resultRedirect->setUrl($this->url->getUrl('sublogins/account/listsubaccount/'));
    }

}
