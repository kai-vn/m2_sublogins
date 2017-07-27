<?php

namespace SITC\Sublogins\Controller\Account;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use SITC\Sublogins\Model\Create;
class ListSubAccount extends Action
{
    protected $_modelCreateFactory;
    protected $_resultPageFactory;

    public function __construct(Context $context,  Create $modelCreateFactory, PageFactory $pageFactory)
    {
        $this->_resultPageFactory = $pageFactory;
        parent::__construct($context);
        $this->_modelCreateFactory = $modelCreateFactory;
    }

    public function execute()
    {

        $newsModel = $this->_objectManager->create('SITC\Sublogins\Model\Create');
        // Get news collection
        $newsCollection = $newsModel->getCollection();
        // Load all data of collection
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' List Sub Account '));

        return $resultPage;
    }
}