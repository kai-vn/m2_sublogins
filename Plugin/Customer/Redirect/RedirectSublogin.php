<?php
namespace SITC\Sublogins\Plugin\Customer\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Api\CustomerMetadataInterface;
class RedirectSublogin
{
    /**
     * Customer Repository.
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepository;
    protected $customerFactory;
    protected $helper;
    protected $redirect;
    protected $customerDataFactory;
    /**
     * Encryptor.
     *
     * @var \Magento\Framework\Encryption\Encryptor
     */
    protected $_encryptor;

    /**
     * Customer registry.
     *
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $_customerRegistry;

    /**
     * AccountManagementPlugin constructor.
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Encryption\Encryptor           $encryptor
     * @param \Magento\Customer\Model\CustomerRegistry          $customerRegistry
     */
    public function __construct(
        Context $context,
        \SITC\Sublogins\Helper\Data $helper,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Response\Http $response,
        ResultFactory $resultFactory,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    ) {
        $this->_url = $url;
        $this->customerDataFactory = $customerDataFactory;
        $this->_response = $response;
        $this->resultFactory = $resultFactory;
        $this->helper = $helper;
        $this->_customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }

    public function aroundExecute(\Magento\Customer\Controller\Adminhtml\Index\Save $subject , \Closure $proceed)
    {
        $resultRedirect = $proceed();
        $customerId = $this->getCurrentCustomerId($subject);
        $customerSublogin = $this->customerFactory->create()->load($customerId);
        if ($this->helper->isSublogin($customerSublogin)) {
            $url = $this->_url->getUrl('sublogins/account/index');
            $resultRedirect->setPath($url);
        }
        return $resultRedirect;
    }

    private function getCurrentCustomerId($subject)
    {
        $originalRequestData = $subject->getRequest()->getPostValue(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER);

        $customerId = isset($originalRequestData['entity_id'])
            ? $originalRequestData['entity_id']
            : null;

        return $customerId;
    }
}