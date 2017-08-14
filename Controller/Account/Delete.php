<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */

namespace SITC\Sublogins\Controller\Account;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\View\Result\PageFactory;

class Delete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $pageFactory;
    protected $customerFactory;
    protected $registry;
    protected $_customer;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    protected $resultRedirect;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\CustomerFactory $customerFactory

    )
    {
        $this->pageFactory = $pageFactory;
        $this->customerFactory = $customerFactory;
        $this->_customer = $customer;
        $this->registry = $registry;
        parent::__construct($context);

    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerId = $this->_request->getParam('id');
        $this->registry->register('isSecureArea', true);
        $customer = $this->customerFactory->create()->load($customerId);
        $customer->delete();
        return $resultRedirect->setUrl($this->_url->getUrl('sublogins/account/listsubaccount'));
    }
}
