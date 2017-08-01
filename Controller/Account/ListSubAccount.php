<?php

namespace SITC\Sublogins\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class ListSubAccount extends Action
{
    protected $_resultPageFactory;

    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        PageFactory $pageFactory
    )
    {
        $this->_resultPageFactory = $pageFactory;
        parent::__construct($context);

    }
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' List Sub Account '));
        return $resultPage;
    }
}