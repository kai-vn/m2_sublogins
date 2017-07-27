<?php

namespace SITC\Sublogins\Controller\Create;


use Magento\Customer\Model\Registration;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Account extends \Magento\Customer\Controller\AbstractAccount
{
    /** @var Registration */
    protected $registration;

    /**
     * @var Session
     */
    protected $session;
    protected $storeManager;
    protected $_resultPageFactory;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory    $customerFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Session $customerSession,
        Registration $registration,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->session = $customerSession;
        $this->registration = $registration;
        $this->_resultPageFactory = $pageFactory;
        $this->storeManager     = $storeManager;
        $this->customerFactory  = $customerFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
//        $resultRedirect = $this->resultRedirectFactory->create();
//        $resultRedirect->setPath('sublogins/account/');
//        return $resultRedirect;
        }
        $resultPage = $this->_resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->prepend(__(' Create Account '));


        return $resultPage;
    }
}