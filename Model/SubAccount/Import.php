<?php
namespace SITC\Sublogins\Model\SubAccount;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Api\AccountManagementInterface;
class Import extends \Magento\ImportExport\Model\AbstractModel
{
    protected $_fileUploaderFactory;
    protected $_logger;
    protected $fileCsv;
    protected $storeManager;
    protected $customerDataFactory;
    protected $dataObjectHelper;
    protected $customerAccountManagement;
    protected $_customerRepositoryInterface;
    protected $customerModel;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\File\Csv $csv,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CustomerInterfaceFactory $customerDataFactory,
        AccountManagementInterface $customerAccountManagement,
        DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\Customer $customer,
        array $data = []
    )
    {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_logger = $logger;
        $this->fileCsv = $csv;
        $this->storeManager = $storeManager;
        $this->customerDataFactory = $customerDataFactory;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerModel = $customer;
        parent::__construct($logger, $filesystem, $data);
    }

    public function importCustomer($data){

//        $parentcustomerId = $this->_customerRepositoryInterface->get($data['sub_parent_email'],1)->getId();
            $this->customerModel->setWebsiteId(1);
            $customer = $this->customerModel->loadByEmail($data['sub_parent_email']);
            $parentcustomerId = $customer->getId();

            if ($parentcustomerId) {
                try{
                $customer = $this->customerDataFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $customer,
                    $data,
                    '\Magento\Customer\Api\Data\CustomerInterface'
                );

                $customer->setCustomAttribute('is_sub_login', 1);
                $customer->setCustomAttribute('sublogin_parent_id', $parentcustomerId);
                $customer->setCustomAttribute('is_active_sublogin', 1);
                $customer->setCustomAttribute('expire_date', $data['expire_date']);
                $this->customerAccountManagement->createAccount($customer);
                return true;
                } catch (\Exception $e){
                    return false;
                }
            }

        return false;

    }
}