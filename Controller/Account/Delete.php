<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SITC\Sublogins\Controller\Account;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;


class Delete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $pageFactory;
    protected $_customerSession;
    protected $customerFactory;
    protected $_customerRepository;
    protected $registry;
    protected $_customer;
    protected $request;
    protected $_messageManager;
    protected $_coreRegistry = null;
    protected $url;
    protected $_cacheTypeList;
    protected $_cacheFrontendPool;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    protected $resultRedirect;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Customer $customer,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory

    )
    {
        $this->pageFactory = $pageFactory;
        $this->customerFactory = $customerFactory;
        $this->request = $request;
        $this->url = $url;
        $this->_customerRepository = $customerRepository;
        $this->_customerSession = $customerSession;
        $this->_coreRegistry = $coreRegistry;
        $this->_messageManager = $messageManager;
        $this->_customer = $customer;
        $this->registry = $registry;
        parent::__construct($context, $registry);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;

    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */


    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sublogins/create');
        $customerId = $this->request->getParam('id');
        $this->registry->register('isSecureArea', true);
        $customer = $this->customerFactory->create()->load($customerId);
        $customer->delete();
        $types = array('config', 'layout', 'block_html', 'collections', 'reflection', 'db_ddl', 'eav', 'config_integration', 'config_integration_api', 'full_page', 'translate', 'config_webservice');
        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
        return $resultRedirect->setUrl($this->url->getUrl('sublogins/create'));
    }


}
