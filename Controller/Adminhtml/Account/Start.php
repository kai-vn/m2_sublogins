<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */
namespace SITC\Sublogins\Controller\Adminhtml\Account;
ini_set('memory_limit', '2048M');

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;

class Start extends \Magento\Backend\App\Action
{

    protected $_fileUploaderFactory;
    protected $_fileSystem;
    protected $_logger;
    protected $fileCsv;
    protected $storeManager;
    protected $importSubAccount;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem $fileSystem,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\File\Csv $csv,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SITC\Sublogins\Model\SubAccount\Import $importSubAccount
    )
    {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_fileSystem = $fileSystem;
        $this->_logger = $logger;
        $this->fileCsv = $csv;
        $this->storeManager = $storeManager;
        $this->importSubAccount = $importSubAccount;
        parent::__construct($context);
    }

    /**
     * Start import process action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $fileRequest = $this->getRequest()->getFiles('sub_accounts_file');
        if ($fileRequest) {
            $path = $this->_fileSystem->getDirectoryRead(
                DirectoryList::VAR_DIR
            )->getAbsolutePath(
                'Sublogins/SubAccount/'
            );

            $fileImage = $fileRequest['name'];

            try {
                /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
                $uploader = $this->_fileUploaderFactory->create(['fileId' => 'sub_accounts_file']);
                $uploader->setAllowedExtensions(['csv']);
                $uploader->setAllowRenameFiles(false);
                $result = $uploader->save($path, $fileImage);

            } catch (\Exception $e) {
                if ($e->getCode() != \Magento\MediaStorage\Model\File\Uploader::TMP_NAME_EMPTY) {
                    $this->_logger->critical($e);
                }
            }

            $uploadedFile = $result['path'] . $result['file'];
            if (file_exists($uploadedFile)) {
                $importData = $this->fileCsv->getData($uploadedFile);
                $keys = $importData[0];

                foreach ($keys as $key => $value) {
                    $keys[$key] = str_replace(' ', '_', strtolower($value));
                }

                $count = count($importData);
                while (--$count > 0) {
                    $currentData = $importData[$count];
                    $data = array_combine($keys, $currentData);
                    $email = $data['email'];
                    if (!empty($email)) {
                        $this->importSubAccount->importCustomer($data);
                    }

                }

            }
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('sublogins/account/index');
            return $resultRedirect;

        }


    }
}
