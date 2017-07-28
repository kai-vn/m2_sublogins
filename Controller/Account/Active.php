<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SITC\Sublogins\Controller\Account;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
class Active extends \Magento\Customer\Controller\AbstractAccount
{
    protected $customerFactory;
    protected $url;
    protected $customerExtractor;
    protected $registration;
    protected $_customerSession;
    protected $session;
    protected $urlModel;
    protected $registry;
    protected $_customerRepository;
    protected $request;
    protected $customerUrl;
    protected $helper;
    protected $customerDataFactory;
    protected $accountManagement;
    protected $_eavAttribute;

    public function __construct(
        Context $context,
        CustomerUrl $customerUrl,
        UrlFactory $urlFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        Session $customerSession,
        CustomerInterfaceFactory $customerDataFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \SITC\Sublogins\Helper\Data $helper,
        AccountManagementInterface $accountManagement,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        CustomerExtractor $customerExtractor,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\RequestInterface $request

    ) {
        $this->_eavAttribute = $eavAttribute;
        $this->accountManagement = $accountManagement;
        $this->helper = $helper;
        $this->customerDataFactory = $customerDataFactory;
        $this->_customerRepository = $customerRepository;
        $this->request = $request;
        $this->url = $url;
        $this->urlModel = $urlFactory->create();
        $this->customerFactory  = $customerFactory;
        $this->registry = $registry;
        $this->customerUrl = $customerUrl;
        $this->session = $customerSession;
        $this->_customerSession = $customerSession;
        $this->customerExtractor = $customerExtractor;
        parent::__construct($context, $registry);
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
