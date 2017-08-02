<?php
namespace SITC\Sublogins\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;

class BeforeSave implements ObserverInterface
{
    /**
     * Encryption model
     *
     * @var EncryptorInterface
     */
    protected $encryptor;
    protected $request;
    protected $helper;
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
    /**
     * @param EncryptorInterface $encryptor
     * @param CustomerRegistry $customerRegistry
     * @param CustomerRepositoryInterface $customerRepository
     */

    public function __construct(
        EncryptorInterface $encryptor,
        \SITC\Sublogins\Helper\Data $helper,
        CustomerRegistry $customerRegistry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\RequestInterface $request,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository
    )
    {
        $this->customerFactory = $customerFactory;
        $this->helper = $helper;
        $this->_customerSession = $customerSession;
        $this->request = $request;
        $this->encryptor = $encryptor;
        $this->customerRegistry = $customerRegistry;
        $this->customerRepository = $customerRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $password = $observer->getEvent()->getData('request')->getParams('customer')['customer']['password_hash'];
        /** @var \Magento\Customer\Model\Customer $model */
        $model = $observer->getEvent()->getData('customer');
        $customer = $this->customerFactory->create()->getId();
        if($this->helper->isSublogin($customer)) {
            $customerSecure = $this->customerRegistry->retrieveSecureData($model->getId());
            if ($this->encryptor->validateHashVersion($customerSecure->getPasswordHash(), true)) {
                $customerSecure->setPasswordHash($this->encryptor->getHash($password, true));
                $this->customerRepository->save($customer);
            }
        }
    }
}
