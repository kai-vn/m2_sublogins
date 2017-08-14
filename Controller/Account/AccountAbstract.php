<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */
namespace SITC\Sublogins\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class AccountAbstract extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;
    protected $_customerSession;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        PageFactory $pageFactory
    )
    {
        $this->_customerSession = $customerSession;
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * Index Action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->pageFactory->create();
        return $resultPage;
    }

    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->_customerSession->isLoggedIn()) {
            $this->_forward('customer/account/login');
            return $resultRedirect->setUrl($this->_url->getUrl('customer/account/login'));
        }

        return parent::dispatch($request);
    }

}