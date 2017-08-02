<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SITC\Sublogins\Controller\Account;

use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;

class Deactive extends \Magento\Customer\Controller\AbstractAccount
{
    protected $customerFactory;
    protected $url;
    protected $pageFactory;
    protected $_customerRepository;
    protected $request;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\UrlInterface $url,
        CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->pageFactory = $pageFactory;
        $this->request = $request;
        $this->urlModel = $urlFactory->create();
        $this->customerFactory = $customerFactory;
        $this->url = $url;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerId = $this->request->getParam('deac');
        $customer = $this->customerFactory->create()->load($customerId);
        $customerData = $customer->getDataModel();
        $customerData->setCustomAttribute('is_active_sublogin', 0);
        $customer->updateData($customerData);
        $customer->save();
        return $resultRedirect->setUrl($this->url->getUrl('sublogins/account/listsubaccount/'));

    }

}
