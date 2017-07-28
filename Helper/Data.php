<?php


namespace SITC\Sublogins\Helper;

use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlInterface;


/**
 * Catalog data helper
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $customerCollectionFactory;
    /**
     * Currently selected store ID if applicable
     *
     * @var int
     */
    protected $storeManager;
    protected $_storeId;
    protected $orders;
    protected $customerFactory;
    /**
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    protected $_orderCollectionFactory;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    protected $customerRepository;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Framework\DB\Helper
     */
    protected $_resourceHelper;
    protected $eavConfig;
    /**
     * @var CustomerSession
     */
    protected $_customerSession;
    protected $_customerRepositoryInterface;
    /**
     * @var \Magento\MediaStorage\Model\File\Uploader
     */
    protected $_uploaderFactory;
    protected $request;
    /**
     * @var Filesystem
     */
    protected $_fileSystem;
    protected $customerExtractor;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;
    protected $urlBuilder;
    protected $_jsonEncoder;
    protected $_eavAttribute;
    protected $customerRepositoryInterface;
    /**
     * category collection factory.
     *
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Registry $coreRegistry
     * @param CustomerSession $customerSession
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        CustomerExtractor $customerExtractor,
        \Magento\Framework\App\RequestInterface $request,
        CustomerCollectionFactory $customerCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        CustomerSession $customerSession,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    )
    {
        $this->_eavAttribute = $eavAttribute;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->_resource = $resource;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerExtractor = $customerExtractor;
        $this->_storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_backendUrl = $backendUrl;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_resourceHelper = $resourceHelper;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_fileSystem = $fileSystem;
        $this->_localeDate = $localeDate;
        $this->_jsonEncoder = $jsonEncoder;

        parent::__construct($context);
    }

    /**
     * Set a specified store ID value
     *
     * @param int $store
     * @return $this
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }

    public function getIdSub()
    {
        $attributeId = $this->_eavAttribute->getIdByCode('customer', 'sublogin_parent_id');
        $customerId = $this->_customerSession->getCustomerId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sub_accs = array();
        $aguments = $objectManager->create('SITC\Sublogins\Model\Parrent')->getCollection()->getData();

        foreach ($aguments as $agument) {
            if ($agument['value'] == $customerId && $agument['attribute_id'] == $attributeId) {
                $sub_accs[] = $agument['entity_id'];
            }
        }

        return $sub_accs;
    }

    public function isSublogin()
    {
        $attributeId = $this->_eavAttribute->getIdByCode('customer', 'is_sub_login');
        $customerId = $this->_customerSession->getCustomerId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sub_accs = array();
        $aguments = $objectManager->create('SITC\Sublogins\Model\Parrent')->getCollection()->getData();
        foreach ($aguments as $agument) {
            if ($agument['value'] == 1 && $agument['entity_id'] == $customerId && $agument['attribute_id'] == $attributeId) {
                $sub_accs[] = $agument['entity_id'];
            }
        }
        return $sub_accs;
    }

    public function getCreateSublogin()
    {
        $customerId = $this->_customerSession->getCustomerId();
        $customer = $this->_customerRepositoryInterface->getById($customerId);
        $customAttribute = $customer->getCustomAttribute('can_create_sub_login');
        if (!empty($customAttribute)) {
            $customAttribute = $customAttribute->getValue();
        }
        return $customAttribute;
    }

    public function getOrdersSubAc()
    {
        $attributeId = $this->_eavAttribute->getIdByCode('customer', 'sublogin_parent_id');
        $customerId = $this->_customerSession->getCustomerId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sub_accs = array();
        $aguments = $objectManager->create('SITC\Sublogins\Model\Parrent')->getCollection()->getData();
        foreach ($aguments as $agument) {
            if ($agument['value'] == $customerId && $agument['attribute_id'] == $attributeId) {
                $sub_accs[] = $agument['entity_id'];
            }
        }
        return $sub_accs;
    }

    public function getOrdersIsSubAc()
    {
        $attributeId = $this->_eavAttribute->getIdByCode('customer', 'is_sub_login');
        $customerId = $this->_customerSession->getCustomerId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sub_accs = array();
        $aguments = $objectManager->create('SITC\Sublogins\Model\Parrent')->getCollection()->getData();
        foreach ($aguments as $agument) {
            if ($agument['value'] == 1 && $agument['entity_id'] == $customerId && $agument['attribute_id'] == $attributeId) {
                $sub_accs[] = $agument['entity_id'];
            }
        }
        return $sub_accs;
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
        return $this->urlBuilder->getUrl('sublogins/account/createpost');
    }


}
