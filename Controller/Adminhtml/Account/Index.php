<?php
namespace SITC\Sublogins\Controller\Adminhtml\Account;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

class Index extends Action
{
    const ADMIN_RESOURCE = 'SITC_Sublogins::account';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SITC_Sublogins::account');
        $resultPage->addBreadcrumb(__('Sublogins'), __('Sublogins'));
        $resultPage->addBreadcrumb(__('Manage Accounts'), __('Manage Accounts'));
        $resultPage->getConfig()->getTitle()->prepend(__('Account'));

        return $resultPage;
    }
}