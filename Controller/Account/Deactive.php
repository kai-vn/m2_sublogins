<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */

namespace SITC\Sublogins\Controller\Account;

use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Result\PageFactory;

class Deactive extends \Magento\Customer\Controller\AbstractAccount
{
    protected $customerFactory;
    protected $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        UrlFactory $urlFactory,
        CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        $this->pageFactory = $pageFactory;
        $this->urlModel = $urlFactory->create();
        $this->customerFactory = $customerFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerId = $this->request->getParam('id');
        $customer = $this->customerFactory->create()->load($customerId);
        $customerData = $customer->getDataModel();
        $customerData->setCustomAttribute('is_active_sublogin', 0);
        $customer->updateData($customerData);
        $customer->save();
        return $resultRedirect->setUrl($this->_url->getUrl('sublogins/account/listsubaccount/'));
    }
}
