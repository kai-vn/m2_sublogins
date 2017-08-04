<?php


namespace SITC\Sublogins\Helper;

use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $customerCollectionFactory;
    protected $subAccounts;
    protected $customerFactory;
    protected $customerRepository;
    protected $_customerSession;
    protected $_customerRepositoryInterface;
    protected $customerExtractor;
    protected $customerRepositoryInterface;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CustomerExtractor $customerExtractor,
        CustomerCollectionFactory $customerCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        CustomerSession $customerSession
    )
    {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->urlBuilder = $urlBuilder;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->_resource = $resource;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerExtractor = $customerExtractor;
        $this->_storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->_customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        parent::__construct($context);
    }

    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }

    public function getSubAccounts()
    {
        if (!$this->subAccounts) {
            $customerId = $this->_customerSession->getCustomerId();
            $this->subAccounts = $this->customerCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('is_sub_login',  \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_SUB_LOGIN)
                ->addAttributeToFilter('sublogin_parent_id', $customerId);
        }
        return $this->subAccounts;
    }

    public function isSublogin($customer = null)
    {
        if (!$customer) {
            $customer = $this->_customerSession->getCustomer();
        }

        if ($customer->getIsSubLogin() == \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_SUB_LOGIN) {
            return true;
        }

        return false;
    }

    public function getCreateSublogin($customer = null)
    {
        if (!$customer) {
            $customer = $this->_customerSession->getCustomer();
        }

        if ($customer->getCanCreateSubLogin()) {
            return true;
        }

        return false;
    }

    public function getStatusSubAc($sublog_id)
    {
        $customer = $this->customerRepositoryInterface->getById($sublog_id);
        $status = $customer->getCustomAttribute('is_active_sublogin');
        if (!empty($status)) {
            $status = $status->getValue();
        }
        return $status;
    }
    public function getSubRegisterPostUrl()
    {
        return $this->_urlBuilder->getUrl('sublogins/account/createpost');
    }
}
