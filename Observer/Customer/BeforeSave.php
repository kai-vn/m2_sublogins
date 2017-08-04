<?php
namespace SITC\Sublogins\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;

class BeforeSave implements ObserverInterface
{
    protected $_responseFactory;
    protected $_url;
    private $logger;
    /**
     * Encryption model
     *
     * @var EncryptorInterface
     */
    protected $_objectManager;
    protected $_urlInterface;
    protected $redirect;
    protected $encryptor;
    protected $request;
    protected $helper;
    protected $_customerRepositoryInterface;
    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;
    protected $customerFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    protected $_customerSession;
    protected $customerDataFactory;
    /**
     * @param EncryptorInterface $encryptor
     * @param CustomerRegistry $customerRegistry
     * @param CustomerRepositoryInterface $customerRepository
     */

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        EncryptorInterface $encryptor,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \SITC\Sublogins\Helper\Data $helper,
        CustomerInterfaceFactory $customerDataFactory,
        CustomerRegistry $customerRegistry,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\RequestInterface $request,
        CustomerSession $customerSession

    )
    {
        $this->logger = $logger;
        $this->_objectManager = $objectManager;
        $this->_responseFactory = $responseFactory;
        $this->_urlInterface = $urlInterface;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->helper = $helper;
        $this->_customerSession = $customerSession;
        $this->request = $request;
        $this->customerDataFactory = $customerDataFactory;
        $this->encryptor = $encryptor;
        $this->customerRegistry = $customerRegistry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $password = $observer->getEvent()->getData('request')->getParams('customer')['customer']['password_hash'];
        $confirmPassword = $observer->getEvent()->getData('request')->getParams('customer')['customer']['password_confirmation'];
        if($confirmPassword != $password) {
            throw new LocalizedException(__('Password do not match.'));
        }
        $model = $observer->getEvent()->getData('customer');
        $customer = $this->customerRepository->getById($model->getId());
        $customerSublogin = $this->customerFactory->create()->load($model->getId());
        if($this->helper->isSublogin($customerSublogin)) {
            $customerSecure = $this->customerRegistry->retrieveSecureData($model->getId());
            if ($this->encryptor->validateHashVersion($customerSecure->getPasswordHash(), true)) {
                $customerSecure->setPasswordHash($this->encryptor->getHash($password, true));
                $this->customerRepository->save($customer);
            }
        }
        }
}
